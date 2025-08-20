<?php
declare(strict_types=1);
ini_set('max_execution_time',0);
ini_set('memory_limit','512M');

$cli = (php_sapi_name() === 'cli');

// ----------------------
// Handle input
// ----------------------
if($cli){
    echo "Enter start URL: ";
    $startUrl = trim(fgets(STDIN));
    echo "Enter max depth: ";
    $maxDepth = (int)trim(fgets(STDIN));
}else{
    $startUrl = $_GET['url'] ?? '';
    $maxDepth = (int)($_GET['depth'] ?? 3);
    if(!$startUrl){
        echo '<form method="get">
                <label>Start URL:</label>
                <input type="text" name="url" value="" size="50"><br><br>
                <label>Max Depth:</label>
                <input type="number" name="depth" value="3" min="1" max="10"><br><br>
                <button type="submit">Start Crawl</button>
              </form>';
        exit;
    }
}

echo "Crawling started for: $startUrl Depth: $maxDepth\n";

// ----------------------
// Config
// ----------------------
$delayMicro = 150000; // 0.15s delay
$excludePaths = ['/blog','/tag','/wp-'];
$excludeExtensions = ['jpg','jpeg','png','gif','svg','webp','ico','css','js','map','woff','woff2','ttf','eot','pdf','zip','rar','7z','gz','mp4','webm','avi','mov'];
$visited = [];

// ----------------------
// Helper functions
// ----------------------
function normalizeUrl(string $base,string $href): ?string{
    $href = trim($href);
    if($href==='' || str_starts_with(strtolower($href),'javascript:') || str_starts_with(strtolower($href),'mailto:') || str_starts_with(strtolower($href),'tel:') || $href[0]==='#') return null;
    $baseParts = parse_url($base);
    if(!$baseParts || empty($baseParts['scheme']) || empty($baseParts['host'])) return null;
    $parts = parse_url($href);
    $abs = $parts && !empty($parts['scheme']) ? $href : (str_starts_with($href,'/') ? $baseParts['scheme'].'://'.$baseParts['host'].$href : $baseParts['scheme'].'://'.$baseParts['host'].rtrim(dirname($baseParts['path']??'/').'/','/').'/'.$href);
    $abs = strtok($abs,'#');
    return rtrim($abs,'/');
}

// ----------------------
// Crawl function
// ----------------------
function crawl(array &$queue, array &$visited, string $startUrl, int $maxDepth, int $delayMicro, array $excludePaths, array $excludeExtensions){
    $ua = "SimplePHPBot/1.0";
    $context = stream_context_create(['http'=>['timeout'=>12,'header'=>"User-Agent: $ua\r\n"]]);

    while(!empty($queue)){
        [$url,$depth] = array_shift($queue);
        if(isset($visited[$url])) continue;
        $visited[$url] = true;

        echo "[Depth $depth] Crawling: $url\n";

        if($depth >= $maxDepth) continue;

        $html = @file_get_contents($url,false,$context);
        if($html !== false && preg_match_all('/<a\s+[^>]*href=["\']?([^"\' >]+)["\']?/i',$html,$matches)){
            foreach($matches[1] as $href){
                $abs = normalizeUrl($url,$href);
                if(!$abs) continue;
                if(parse_url($abs,PHP_URL_HOST) !== parse_url($startUrl,PHP_URL_HOST)) continue;
                if(isset($visited[$abs])) continue;

                $ext = strtolower(pathinfo(parse_url($abs,PHP_URL_PATH)??'',PATHINFO_EXTENSION));
                if($ext && in_array($ext,$excludeExtensions,true)) continue;

                $skip = false;
                foreach($excludePaths as $p){
                    if(stripos($abs,$p)!==false){ $skip=true; break; }
                }
                if($skip) continue;

                $queue[] = [$abs, $depth+1];
            }
        }

        usleep($delayMicro);
    }
}

// ----------------------
// Start crawling
// ----------------------
$queue = [[$startUrl,0]];
crawl($queue, $visited, $startUrl, $maxDepth, $delayMicro, $excludePaths, $excludeExtensions);

// ----------------------
// Save sitemap.xml
// ----------------------
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
$xml->addAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
foreach(array_keys($visited) as $u){
    $urlNode = $xml->addChild('url');
    $urlNode->addChild('loc',htmlspecialchars($u,ENT_QUOTES|ENT_XML1));
}

file_put_contents('sitemap.xml',$xml->asXML());

echo "\nCrawl finished. ".count($visited)." URLs saved to sitemap.xml\n";

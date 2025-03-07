<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

// 安全验证 - 只允许GET请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $musicDir = 'C:/随机图片/music/one'; // 实际音乐目录路径
    $baseUrl = 'https://tutu.rinai.top/music/one/';
    
    // 验证目录可访问性
    if (!is_dir($musicDir)) {
        throw new Exception("音乐目录不存在: $musicDir");
    }

    $musicFiles = [];
    foreach (new DirectoryIterator($musicDir) as $file) {
        if ($file->isDot() || $file->isDir()) continue;
        
        // 仅处理MP3文件
        if (strtolower($file->getExtension()) === 'mp3') {
            $rawFilename = $file->getFilename();
            
            // 返回相对路径（关键修复）
            $musicFiles[] = [
                'name' => pathinfo($rawFilename, PATHINFO_FILENAME),
                'url' => rawurlencode($rawFilename), // 仅编码文件名
                'duration' => '4:41', // 示例值，需实际解析
                'bitrate' => 128      // 示例值，需实际解析
            ];
        }
    }

    // 按文件名排序
    usort($musicFiles, fn($a, $b) => strcmp($a['name'], $b['name']));

    echo json_encode([
        'status' => 'success',
        'data' => $musicFiles
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

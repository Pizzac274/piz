<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/logger.php';

$message = '';
$error = '';
$searchStartDate = '';
$searchEndDate = '';

// 從會話中獲取消息
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

// 處理管理員二次驗證
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_verify'])) {
    $adminPassword = trim($_POST['admin_password'] ?? '');
    
    if (empty($adminPassword)) {
        $_SESSION['error'] = '請輸入管理員密碼';
    } else {
        // 讀取員工資料
        $members = file('data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $isAdmin = false;
        
        // 跳過表頭
        for ($i = 1; $i < count($members); $i++) {
            $fields = explode(' ', $members[$i]);
            if ($fields[0] === $_SESSION['employee_id'] && $fields[3] === $adminPassword) {
                $isAdmin = true;
                break;
            }
        }
        
        if ($isAdmin) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_verified'] = true;
            $_SESSION['message'] = '管理員驗證成功！';
            unset($_SESSION['error']);
            Logger::info("管理員 {$_SESSION['employee_name']} 驗證成功");
        } else {
            $_SESSION['error'] = '管理員密碼錯誤';
            Logger::warning("管理員驗證失敗：員工 {$_SESSION['employee_name']}");
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 處理登入請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $employeeId = trim($_POST['employee_id'] ?? '');
    $punchPassword = trim($_POST['punch_password'] ?? '');
    
    if (empty($employeeId) || empty($punchPassword)) {
        $_SESSION['error'] = '請輸入員工編號和打卡密碼';
    } else {
        // 讀取員工資料
        $members = file('data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $employeeFound = false;
        $employeeName = '';
        $isAdmin = false;
        
        // 跳過表頭
        for ($i = 1; $i < count($members); $i++) {
            $fields = explode(' ', $members[$i]);
            if ($fields[0] === $employeeId && $fields[2] === $punchPassword) {
                $employeeFound = true;
                $employeeName = $fields[1];
                // 檢查是否為管理員
                $isAdmin = ($employeeId === ADMIN_EMPLOYEE_ID);
                break;
            }
        }
        
        if ($employeeFound) {
            // 設置會話變量
            $_SESSION['employee_id'] = $employeeId;
            $_SESSION['employee_name'] = $employeeName;
            $_SESSION['is_admin'] = $isAdmin;
            $_SESSION['message'] = '登入成功！' . ($isAdmin ? '（管理員模式）' : '');
            unset($_SESSION['error']);
            Logger::info("員工 {$employeeName} 登入成功" . ($isAdmin ? '（管理員模式）' : ''));
        } else {
            $_SESSION['error'] = '員工編號或打卡密碼錯誤';
            Logger::warning("登入失敗：員工編號 {$employeeId}");
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 處理打卡請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['punch'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px;'>";
    echo "<h3>打卡調試信息</h3>";
    echo "<pre>收到打卡請求：";
    print_r($_POST);
    echo "</pre>";
    
    if (isset($_SESSION['employee_id']) && isset($_SESSION['employee_name'])) {
        $employeeId = $_SESSION['employee_id'];
        $employeeName = $_SESSION['employee_name'];
        $shift = $_POST['shift'] ?? '未知班別';
        $punchTime = date('Y-m-d H:i:s');
        
        echo "<p>員工ID: $employeeId</p>";
        echo "<p>姓名: $employeeName</p>";
        echo "<p>班別: $shift</p>";
        echo "<p>打卡時間: $punchTime</p>";
        
        // 使用絕對路徑
        $filePath = __DIR__ . '/data/checktime.txt';
        echo "<p>文件路徑: $filePath</p>";
        
        // 檢查目錄是否存在，不存在則創建
        $dirPath = dirname($filePath);
        if (!is_dir($dirPath)) {
            if (mkdir($dirPath, 0777, true)) {
                echo "<p>成功創建目錄: $dirPath</p>";
            } else {
                echo "<p>創建目錄失敗: $dirPath</p>";
            }
        }
        
        // 首先嘗試寫入測試文件
        $testFile = __DIR__ . '/test_write.txt';
        if (file_put_contents($testFile, "Test at " . date('Y-m-d H:i:s') . "\n")) {
            echo "<p>測試文件寫入成功</p>";
        } else {
            echo "<p>測試文件寫入失敗: " . error_get_last()['message'] . "</p>";
        }
        
        // 重新創建打卡文件
        $header = "員工編號 員工姓名 打卡時間 班別\n";
        $newRecord = "\n$employeeId $employeeName $punchTime $shift";
        
        // 先嘗試讀取現有文件內容
        $currentContents = "";
        if (file_exists($filePath)) {
            $currentContents = file_get_contents($filePath);
            echo "<p>現有文件內容: " . nl2br(htmlspecialchars($currentContents)) . "</p>";
        }
        
        // 嘗試直接寫入新文件
        $success = false;
        
        // 方法1: 使用 file_put_contents 寫入完整內容
        if (!$currentContents) {
            // 文件不存在或為空，寫入標頭和記錄
            $content = $header . $newRecord;
            $result = file_put_contents($filePath, $content);
        } else {
            // 文件存在，追加記錄
            $result = file_put_contents($filePath, $currentContents . $newRecord);
        }
        
        if ($result !== false) {
            $success = true;
            echo "<p>方法1成功: 寫入了 $result 個字節</p>";
        } else {
            echo "<p>方法1失敗: " . error_get_last()['message'] . "</p>";
            
            // 方法2: 使用 fopen/fwrite
            $file = fopen($filePath, 'w');
            if ($file) {
                if (!$currentContents) {
                    fwrite($file, $header);
                } else {
                    fwrite($file, $currentContents);
                }
                fwrite($file, $newRecord);
                fclose($file);
                $success = true;
                echo "<p>方法2成功</p>";
            } else {
                echo "<p>方法2失敗: 無法打開文件</p>";
            }
        }
        
        // 最終結果
        if ($success) {
            echo "<p style='color: green;'>打卡成功！</p>";
            $_SESSION['message'] = '打卡成功';
        } else {
            echo "<p style='color: red;'>所有寫入方法都失敗了</p>";
            $_SESSION['error'] = '打卡記錄失敗，請稍後再試';
        }
        
        // 檢查文件是否成功寫入
        echo "<p>檢查文件是否存在: " . (file_exists($filePath) ? '是' : '否') . "</p>";
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            echo "<p>文件內容: " . nl2br(htmlspecialchars($fileContent)) . "</p>";
            
            if (strpos($fileContent, $employeeId) !== false && strpos($fileContent, $punchTime) !== false) {
                echo "<p style='color: green;'>文件中找到新記錄</p>";
            } else {
                echo "<p style='color: red;'>文件中未找到新記錄</p>";
            }
        }
    } else {
        echo "<p>會話信息缺失，請重新登入</p>";
        $_SESSION['error'] = '打卡失敗，請重新登入';
    }
    
    echo "<div><a href='" . $_SERVER['PHP_SELF'] . "' class='btn btn-primary'>返回主頁</a></div>";
    echo "</div>";
    exit; // 停止執行，顯示調試信息
}

// 處理查詢請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchStartDate = $_POST['start_date'] ?? '';
    $searchEndDate = $_POST['end_date'] ?? '';
    $_SESSION['search_start_date'] = $searchStartDate;
    $_SESSION['search_end_date'] = $searchEndDate;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 處理登出請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (isset($_SESSION['employee_name'])) {
        Logger::info("員工 {$_SESSION['employee_name']} 登出系統");
    }
    // 清除所有會話變量
    session_unset();
    // 銷毀會話
    session_destroy();
    // 重定向到首頁
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 從會話中獲取搜索條件
if (isset($_SESSION['search_start_date'])) {
    $searchStartDate = $_SESSION['search_start_date'];
    unset($_SESSION['search_start_date']);
}
if (isset($_SESSION['search_end_date'])) {
    $searchEndDate = $_SESSION['search_end_date'];
    unset($_SESSION['search_end_date']);
}

// 分頁設置
$recordsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage); // 確保頁碼至少為1

// 讀取打卡記錄
$punchRecords = [];
$totalRecords = 0;
if (file_exists('data/checktime.txt') && isset($_SESSION['employee_id'])) {
    $records = file('data/checktime.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // 跳過表頭
    for ($i = 1; $i < count($records); $i++) {
        $fields = preg_split('/\s+/', trim($records[$i]));
        if (count($fields) >= 4 && $fields[0] === $_SESSION['employee_id']) {
            // 解析日期時間
            $punchTime = $fields[2];
            $dateTime = explode(' ', $punchTime);
            if (count($dateTime) >= 2) {
                $date = $dateTime[0];
                $time = $dateTime[1];

                // 檢查日期範圍
                if (!empty($searchStartDate) && !empty($searchEndDate)) {
                    if ($date >= $searchStartDate && $date <= $searchEndDate) {
                        $punchRecords[] = [
                            'employee_id' => $fields[0],
                            'employee_name' => $fields[1],
                            'date' => $date,
                            'time' => $time,
                            'shift' => $fields[3] // 新增班別欄位
                        ];
                    }
                } else {
                    $punchRecords[] = [
                        'employee_id' => $fields[0],
                        'employee_name' => $fields[1],
                        'date' => $date,
                        'time' => $time,
                        'shift' => $fields[3] // 新增班別欄位
                    ];
                }
            }
        }
    }
    // 反轉數組以顯示最新的記錄在前面
    $punchRecords = array_reverse($punchRecords);
    $totalRecords = count($punchRecords);
}

// 計算總頁數
$totalPages = ceil($totalRecords / $recordsPerPage);

// 獲取當前頁的記錄
$startIndex = ($currentPage - 1) * $recordsPerPage;
$currentPageRecords = array_slice($punchRecords, $startIndex, $recordsPerPage);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            --secondary-gradient: linear-gradient(135deg, #4FACFE 0%, #00F2FE 100%);
            --success-gradient: linear-gradient(135deg, #43E97B 0%, #38F9D7 100%);
            --background-gradient: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            --card-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            --text-primary: #ffffff;
            --text-secondary: #b8c6db;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 8px 15px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 15px 30px rgba(0, 0, 0, 0.3);
            --glow: 0 0 20px rgba(255, 107, 107, 0.3);
        }

        body {
            font-family: 'Microsoft JhengHei', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background-gradient);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 107, 107, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(79, 172, 254, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: var(--glow);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
            }
            to {
                text-shadow: 0 0 20px rgba(255, 107, 107, 0.5),
                            0 0 30px rgba(255, 107, 107, 0.3);
            }
        }

        .card {
            background: var(--card-gradient);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .card:hover::before {
            transform: translateX(100%);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg), var(--glow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #FF6B6B;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
            outline: none;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .btn:hover::before {
            transform: translateX(100%);
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .btn-secondary {
            background: var(--secondary-gradient);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }

        .records-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
        }

        .records-table th,
        .records-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .records-table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .records-table tr {
            transition: all 0.3s ease;
        }

        .records-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.01);
        }

        .records-table td {
            color: var(--text-secondary);
        }

        .no-records {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            margin-top: 1.5rem;
        }

        .no-records i {
            font-size: 3rem;
            color: #FF6B6B;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .stats-card {
            background: var(--card-gradient);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stats-title {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            background: var(--card-gradient);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .pagination .active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2.5rem;
            }

            .card {
                padding: 1.5rem;
            }

            .btn {
                padding: 0.75rem 1.5rem;
            }
        }

        /* 動畫效果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card, .header, .stats-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* 自定義滾動條 */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gradient);
        }

        /* 粒子效果 */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }

        /* 錯誤提示樣式 */
        .error-message {
            background: linear-gradient(135deg, #ff4b4b 0%, #ff7676 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin: 1rem 0;
            box-shadow: 0 0 20px rgba(255, 75, 75, 0.3);
            position: relative;
            overflow: hidden;
            animation: errorShake 0.5s ease-in-out;
            transform-origin: center;
        }

        .error-message::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: translateX(-100%);
            animation: errorShine 2s infinite;
        }

        .error-message i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
            animation: errorIconPulse 1s infinite;
        }

        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes errorShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes errorIconPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* 自定義模態框樣式 */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 1000;
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--card-gradient);
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 0 30px rgba(255, 107, 107, 0.3);
            border: 1px solid var(--border-color);
            width: 90%;
            max-width: 500px;
            text-align: center;
            animation: modalSlideIn 0.5s ease-out;
        }

        .modal-title {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .modal-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .modal-btn.confirm {
            background: var(--primary-gradient);
            color: white;
        }

        .modal-btn.cancel {
            background: var(--secondary-gradient);
            color: white;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .modal-btn:hover::before {
            transform: translateX(100%);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modalSlideIn {
            from { 
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            to { 
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        /* 成功提示樣式 */
        .success-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(67, 233, 123, 0.3);
            z-index: 1001;
            display: none;
            animation: toastSlideIn 0.5s ease-out;
        }

        @keyframes toastSlideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .success-toast i {
            font-size: 2rem;
            margin-bottom: 1rem;
            animation: successIconPulse 1s infinite;
        }

        @keyframes successIconPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* 登入成功提示樣式 */
        .login-success-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(67, 233, 123, 0.3);
            z-index: 1001;
            display: none;
            animation: toastSlideIn 0.5s ease-out;
        }

        .login-success-toast i {
            font-size: 2rem;
            margin-bottom: 1rem;
            animation: successIconPulse 1s infinite;
        }

        .login-success-toast h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .login-success-toast p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        /* 模態框樣式 */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            position: relative;
            background-color: var(--background-card);
            margin: 10% auto;
            padding: 0;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: modalFadeIn 0.3s;
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--text-primary);
        }

        .modal-body {
            padding: 20px;
            color: var(--text-secondary);
        }

        .modal-body p {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .modal-footer {
            padding: 15px;
            text-align: center;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-30px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-gamepad"></i> <?php echo SITE_NAME; ?></h1>
        </div>

        <?php if (!isset($_SESSION['employee_id'])): ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                    echo $_SESSION['error'];
                    ?>
                </div>
            <?php endif; ?>
            <div class="card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="employee_id"><i class="fas fa-id-card"></i> 員工編號</label>
                        <input type="text" id="employee_id" name="employee_id" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="punch_password"><i class="fas fa-key"></i> 打卡密碼</label>
                        <input type="password" id="punch_password" name="punch_password" class="form-control" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> 登入
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="stats-card">
                    <div class="stats-title">歡迎回來</div>
                    <div class="stats-value"><?php echo $_SESSION['employee_name']; ?></div>
                </div>

                <form id="punchForm" method="POST" action="">
                    <div class="form-group">
                        <label for="shift"><i class="fas fa-clock"></i> 選擇班別</label>
                        <select id="shift" name="shift" class="form-control" required>
                            <option value="上午上班">上午上班</option>
                            <option value="下午下班">下午下班</option>
                            <option value="下午上班">下午上班</option>
                            <option value="晚上下班">晚上下班</option>
                        </select>
                    </div>
                    <button type="submit" id="punchBtn" name="punch" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> 打卡簽到
                    </button>
                </form>

                <form method="POST" action="" style="margin-top: 1rem;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" name="logout" class="btn btn-secondary" onclick="return confirm('確定要登出嗎？')">
                        <i class="fas fa-sign-out-alt"></i> 登出
                    </button>
                </form>

                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <div class="admin-panel">
                        <h3><i class="fas fa-shield-alt"></i> 管理員功能</h3>
                        <?php if (!isset($_SESSION['admin_verified'])): ?>
                            <form method="POST" action="" class="admin-verify-form">
                                <div class="form-group">
                                    <label for="admin_password"><i class="fas fa-lock"></i> 管理員密碼</label>
                                    <input type="password" id="admin_password" name="admin_password" class="form-control" required>
                                </div>
                                <button type="submit" name="admin_verify" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-check"></i> 驗證管理員身份
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="admin-buttons" style="display: flex; flex-direction: column; gap: 1rem;">
                                <a href="admin/attendance_records.php" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-clock"></i> 查看打卡記錄
                                </a>
                                <a href="admin/manage_employees.php" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-users-cog"></i> 管理員工建檔
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                    <i class="fas fa-history"></i> 打卡記錄
                </h3>

                <form method="GET" class="filter-form" style="margin-bottom: 2rem;">
                    <div class="form-group">
                        <label for="start_date"><i class="fas fa-calendar-alt"></i> 開始日期</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" 
                               value="<?php echo $searchStartDate; ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_date"><i class="fas fa-calendar-alt"></i> 結束日期</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" 
                               value="<?php echo $searchEndDate; ?>">
                    </div>

                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> 查詢
                        </button>
                    </div>
                </form>

                <?php if (empty($currentPageRecords)): ?>
                    <div class="no-records">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>沒有找到符合條件的打卡記錄</h3>
                        <?php if (isset($_GET['start_date']) || isset($_GET['end_date'])): ?>
                            <p>篩選條件：<?php 
                                echo "日期範圍：" . $searchStartDate . " 至 " . $searchEndDate;
                            ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="stats-card">
                        <div class="stats-title">查詢結果統計</div>
                        <div class="stats-value">共 <?php echo count($currentPageRecords); ?> 筆記錄</div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="records-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> 日期</th>
                                    <th><i class="fas fa-clock"></i> 時間</th>
                                    <th><i class="fas fa-clock"></i> 班別</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($currentPageRecords as $record): ?>
                                    <tr>
                                        <td><?php echo $record['date']; ?></td>
                                        <td><?php echo $record['time']; ?></td>
                                        <td><?php echo $record['shift']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?php echo $currentPage - 1; ?>&start_date=<?php echo $searchStartDate; ?>&end_date=<?php echo $searchEndDate; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);

                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <a href="?page=<?php echo $i; ?>&start_date=<?php echo $searchStartDate; ?>&end_date=<?php echo $searchEndDate; ?>" 
                                   class="<?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?php echo $currentPage + 1; ?>&start_date=<?php echo $searchStartDate; ?>&end_date=<?php echo $searchEndDate; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 打卡確認模態框 -->
    <div id="punchConfirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-clock"></i> 確認打卡</h2>
            </div>
            <div class="modal-body">
                <p>您確定要進行打卡簽到嗎？</p>
                <p>員工：<span id="confirmEmployeeName"><?php echo isset($_SESSION['employee_name']) ? $_SESSION['employee_name'] : ''; ?></span></p>
                <p>班別：<span id="confirmShift"></span></p>
            </div>
            <div class="modal-footer">
                <button id="confirmPunchBtn" class="btn btn-primary" onclick="confirmPunch()">確認打卡</button>
                <button class="btn btn-secondary" onclick="closeModal()">取消</button>
            </div>
        </div>
    </div>

    <!-- 添加成功提示 -->
    <div id="successToast" class="success-toast">
        <i class="fas fa-check-circle"></i>
        <h3>打卡成功！</h3>
        <p>已記錄您的打卡時間</p>
    </div>

    <!-- 添加登入成功提示 -->
    <div id="loginSuccessToast" class="login-success-toast">
        <i class="fas fa-user-check"></i>
        <h3>登入成功！</h3>
        <p>歡迎回來，<span id="employeeName"></span></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 粒子效果
        function createParticles() {
            const particles = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particles.appendChild(particle);
            }
        }

        createParticles();

        // 語音功能
        const speech = new SpeechSynthesisUtterance();
        speech.lang = 'zh-TW';
        speech.volume = 1;
        speech.rate = 1;
        speech.pitch = 1;

        function speak(text) {
            speech.text = text;
            window.speechSynthesis.speak(speech);
        }

        // 顯示打卡確認框
        function showPunchConfirm() {
            console.log('顯示打卡確認框');
            var modal = document.getElementById('punchConfirmModal');
            var shiftSelect = document.getElementById('shift');
            var confirmShift = document.getElementById('confirmShift');
            
            if (shiftSelect && confirmShift) {
                confirmShift.textContent = shiftSelect.options[shiftSelect.selectedIndex].text;
                console.log('選擇的班別:', shiftSelect.value);
            }
            
            if (modal) {
                modal.style.display = 'block';
            } else {
                console.error('找不到確認對話框!');
            }
        }

        // 關閉模態框
        function closeModal() {
            console.log('關閉模態框');
            var modal = document.getElementById('punchConfirmModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // 確認打卡 - 修改此函數以直接提交表單
        function confirmPunch() {
            console.log('confirmPunch 函數被調用');
            
            // 直接獲取表單並提交
            var form = document.getElementById('punchForm');
            console.log('提交表單', form);
            
            if (form) {
                console.log('班別值:', form.elements['shift'].value);
                form.submit();
                console.log('表單已提交');
                return true;
            } else {
                console.error('找不到表單元素!');
                return false;
            }
        }

        // 顯示成功提示
        function showSuccessToast() {
            const toast = document.getElementById('successToast');
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // 顯示登入成功提示
        function showLoginSuccess(name) {
            const toast = document.getElementById('loginSuccessToast');
            const nameSpan = document.getElementById('employeeName');
            nameSpan.textContent = name;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // 修改打卡按鈕點擊事件
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM 加載完成，初始化事件處理');
            
            // 為打卡按鈕添加事件監聽器
            var punchBtn = document.getElementById('punchBtn');
            if (punchBtn) {
                console.log('找到打卡按鈕');
                punchBtn.addEventListener('click', function(e) {
                    console.log('打卡按鈕被點擊');
                    e.preventDefault();
                    showPunchConfirm();
                });
            } else {
                console.log('找不到打卡按鈕');
            }
            
            // 檢查表單元素
            var punchForm = document.getElementById('punchForm');
            if (punchForm) {
                console.log('找到打卡表單');
                console.log('表單方法:', punchForm.method);
                console.log('表單目標:', punchForm.action);
                console.log('表單元素:', punchForm.elements);
                
                // 檢查班別選擇器
                var shiftSelect = punchForm.elements['shift'];
                if (shiftSelect) {
                    console.log('找到班別選擇器');
                    console.log('當前班別值:', shiftSelect.value);
                } else {
                    console.log('找不到班別選擇器');
                }
            } else {
                console.log('找不到打卡表單');
            }
        });

        // 頁面加載完成後執行
        window.onload = function() {
            <?php if (isset($_SESSION['message'])): ?>
                <?php if (strpos($_SESSION['message'], '登入成功') !== false): ?>
                    setTimeout(function() {
                        showLoginSuccess('<?php echo $_SESSION['employee_name']; ?>');
                        speak('<?php echo $_SESSION['employee_name']; ?>登入成功');
                    }, 500);
                <?php elseif (strpos($_SESSION['message'], '打卡成功') !== false): ?>
                    showSuccessToast();
                    speak('打卡成功！');
                <?php endif; ?>
            <?php endif; ?>
        };

        // 確保語音功能可用
        function initSpeech() {
            if ('speechSynthesis' in window) {
                // 等待語音引擎準備就緒
                window.speechSynthesis.onvoiceschanged = function() {
                    const voices = window.speechSynthesis.getVoices();
                    // 尋找中文語音
                    const chineseVoice = voices.find(voice => voice.lang.includes('zh'));
                    if (chineseVoice) {
                        speech.voice = chineseVoice;
                    }
                };
            }
        }

        // 初始化語音功能
        initSpeech();
    </script>
</body>
</html> 
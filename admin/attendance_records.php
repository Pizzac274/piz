<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/logger.php';

// 檢查是否為管理員
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

// 讀取員工資料
$members = file('../data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$employees = [];
// 跳過表頭
for ($i = 1; $i < count($members); $i++) {
    $fields = explode(' ', $members[$i]);
    $employees[$fields[0]] = $fields[1]; // 員工編號 => 員工姓名
}

// 處理篩選條件
$selectedEmployee = $_GET['employee'] ?? '';
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// 讀取打卡記錄
$records = [];
if (file_exists('../data/checktime.txt')) {
    $checktime = file('../data/checktime.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // 跳過表頭
    for ($i = 1; $i < count($checktime); $i++) {
        $fields = explode(' ', $checktime[$i]);
        if (count($fields) >= 3) {
            // 解析打卡時間
            $punchTime = $fields[2];
            $dateTime = explode('-', $punchTime);
            if (count($dateTime) >= 4) {
                $recordDate = $dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2];
                $recordTime = $dateTime[3];
                
                // 應用篩選條件
                if (($selectedEmployee === '' || $fields[0] === $selectedEmployee) &&
                    $recordDate >= $startDate && $recordDate <= $endDate) {
                    $records[] = [
                        'employee_id' => $fields[0],
                        'employee_name' => $fields[1],
                        'date' => $recordDate,
                        'time' => $recordTime
                    ];
                }
            }
        }
    }
}

// 按時間倒序排序
usort($records, function($a, $b) {
    $dateA = strtotime($a['date'] . ' ' . $a['time']);
    $dateB = strtotime($b['date'] . ' ' . $b['time']);
    return $dateB - $dateA;
});

// 添加調試信息
if (empty($records)) {
    error_log("No records found with filters: Employee=" . $selectedEmployee . ", StartDate=" . $startDate . ", EndDate=" . $endDate);
}

// 處理刪除請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_record'])) {
        $recordId = $_POST['record_id'];
        $records = file('../data/checktime.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = $records[0]; // 保存表頭
        $newRecords = [$header];
        
        foreach ($records as $index => $record) {
            if ($index > 0 && $index != $recordId) { // 跳過表頭和要刪除的記錄
                $newRecords[] = $record;
            }
        }
        
        file_put_contents('../data/checktime.txt', implode("\n", $newRecords));
        $_SESSION['message'] = '記錄已成功刪除';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    if (isset($_POST['clear_records'])) {
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        $records = file('../data/checktime.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $header = $records[0]; // 保存表頭
        $newRecords = [$header];
        
        foreach ($records as $index => $record) {
            if ($index > 0) { // 跳過表頭
                $fields = explode(' ', $record);
                if (count($fields) >= 3) {
                    $punchTime = $fields[2];
                    $dateTime = explode('-', $punchTime);
                    if (count($dateTime) >= 4) {
                        $date = $dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2];
                        if ($date < $startDate || $date > $endDate) {
                            $newRecords[] = $record;
                        }
                    }
                }
            }
        }
        
        file_put_contents('../data/checktime.txt', implode("\n", $newRecords));
        $_SESSION['message'] = '指定時間段的記錄已清空';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>打卡記錄管理 - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --secondary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --background-gradient: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            --card-gradient: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Microsoft JhengHei', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background-gradient);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: var(--card-gradient);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .header:hover {
            transform: translateY(-2px);
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .header h1 {
            color: var(--text-primary);
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            color: var(--text-secondary);
            margin: 0.5rem 0 0;
            font-size: 1.1rem;
        }

        .card {
            background: var(--card-gradient);
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg), 0 0 20px rgba(99, 102, 241, 0.1);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-secondary {
            background: var(--secondary-gradient);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
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
            background: rgba(99, 102, 241, 0.05);
            font-weight: 600;
            color: var(--text-primary);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .records-table tr {
            transition: all 0.3s ease;
        }

        .records-table tr:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: scale(1.01);
        }

        .records-table td {
            color: var(--text-secondary);
        }

        .no-records {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.5);
            border-radius: 0.75rem;
            margin-top: 1.5rem;
        }

        .no-records i {
            font-size: 3rem;
            color: #6366f1;
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
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stats-title {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .stats-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .records-table {
                display: block;
                overflow-x: auto;
            }

            .header {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }
        }

        /* 動畫效果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
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
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clock"></i> 打卡記錄管理</h1>
            <p>管理員：<?php echo $_SESSION['employee_name']; ?></p>
        </div>

        <div class="card">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="employee"><i class="fas fa-user"></i> 選擇員工</label>
                    <select name="employee" id="employee" class="form-control">
                        <option value="">全部員工</option>
                        <?php foreach ($employees as $id => $name): ?>
                            <option value="<?php echo $id; ?>" <?php echo $selectedEmployee === $id ? 'selected' : ''; ?>>
                                <?php echo $id . ' - ' . $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> 開始日期</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" 
                           value="<?php echo $startDate; ?>">
                </div>

                <div class="form-group">
                    <label for="end_date"><i class="fas fa-calendar-alt"></i> 結束日期</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" 
                           value="<?php echo $endDate; ?>">
                </div>

                <div class="form-group" style="display: flex; align-items: flex-end; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> 查詢
                    </button>
                    <button type="button" class="btn btn-secondary" 
                            onclick="location.href='../index.php'">
                        <i class="fas fa-home"></i> 返回首頁
                    </button>
                </div>
            </form>

            <?php if (empty($records)): ?>
                <div class="no-records">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>沒有找到符合條件的打卡記錄</h3>
                    <?php if (isset($_GET['employee']) || isset($_GET['start_date']) || isset($_GET['end_date'])): ?>
                        <p>篩選條件：<?php 
                            echo "員工：" . ($selectedEmployee ? $selectedEmployee : "全部") . "，";
                            echo "日期範圍：" . $startDate . " 至 " . $endDate;
                        ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="stats-card">
                    <div class="stats-title">查詢結果統計</div>
                    <div class="stats-value">共 <?php echo count($records); ?> 筆記錄</div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-id-card"></i> 員工編號</th>
                                <th><i class="fas fa-user"></i> 員工姓名</th>
                                <th><i class="fas fa-calendar"></i> 日期</th>
                                <th><i class="fas fa-clock"></i> 時間</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo $record['employee_id']; ?></td>
                                    <td><?php echo $record['employee_name']; ?></td>
                                    <td><?php echo $record['date']; ?></td>
                                    <td><?php echo $record['time']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                <i class="fas fa-trash-alt"></i> 批量刪除
            </h3>
            <form method="POST" action="" onsubmit="return confirm('確定要清空這個時間段的所有記錄嗎？此操作不可恢復！');">
                <div class="form-group">
                    <label for="clear_start_date"><i class="fas fa-calendar-alt"></i> 開始日期</label>
                    <input type="date" id="clear_start_date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="clear_end_date"><i class="fas fa-calendar-alt"></i> 結束日期</label>
                    <input type="date" id="clear_end_date" name="end_date" class="form-control" required>
                </div>
                <button type="submit" name="clear_records" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 清空指定時間段記錄
                </button>
            </form>
        </div>

        <?php if (empty($records)): ?>
            <div class="no-records">
                <i class="fas fa-clipboard-list"></i>
                <h3>沒有找到符合條件的打卡記錄</h3>
                <?php if (isset($_GET['start_date']) || isset($_GET['end_date'])): ?>
                    <p>篩選條件：<?php 
                        echo "日期範圍：" . $startDate . " 至 " . $endDate;
                    ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="stats-card">
                <div class="stats-title">查詢結果統計</div>
                <div class="stats-value">共 <?php echo count($records); ?> 筆記錄</div>
            </div>

            <div style="overflow-x: auto;">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> 日期</th>
                            <th><i class="fas fa-clock"></i> 時間</th>
                            <th><i class="fas fa-user"></i> 員工編號</th>
                            <th><i class="fas fa-user-tag"></i> 員工姓名</th>
                            <th><i class="fas fa-cog"></i> 操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $index => $record): ?>
                            <tr>
                                <td><?php echo $record['date']; ?></td>
                                <td><?php echo $record['time']; ?></td>
                                <td><?php echo $record['employee_id']; ?></td>
                                <td><?php echo $record['employee_name']; ?></td>
                                <td>
                                    <form method="POST" action="" style="display: inline;" onsubmit="return confirm('確定要刪除這條記錄嗎？');">
                                        <input type="hidden" name="record_id" value="<?php echo $index + 1; ?>">
                                        <button type="submit" name="delete_record" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> 刪除
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="manage_employees.php" class="btn btn-primary">
                <i class="fas fa-users-cog"></i> 管理員工
            </a>
            <a href="../index.php" class="btn btn-secondary" style="margin-left: 1rem;">
                <i class="fas fa-home"></i> 返回首頁
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
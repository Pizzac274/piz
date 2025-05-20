<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/logger.php';

// 檢查是否為管理員
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

// 處理新增員工請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $employeeId = trim($_POST['employee_id']);
    $employeeName = trim($_POST['employee_name']);
    $punchPassword = trim($_POST['punch_password']);
    $adminPassword = trim($_POST['admin_password']);

    // 讀取現有員工資料
    $members = file('../data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // 檢查員工編號是否已存在
    $exists = false;
    foreach ($members as $index => $member) {
        if ($index > 0) { // 跳過表頭
            $fields = explode(' ', $member);
            if ($fields[0] === $employeeId) {
                $exists = true;
                break;
            }
        }
    }

    if ($exists) {
        $_SESSION['error'] = '員工編號已存在';
    } else {
        // 新增員工資料
        $newMember = $employeeId . ' ' . $employeeName . ' ' . $punchPassword . ' ' . $adminPassword;
        file_put_contents('../data/member.txt', "\n" . $newMember, FILE_APPEND);
        $_SESSION['message'] = '員工資料新增成功';
        Logger::info("管理員 {$_SESSION['employee_name']} 新增員工：{$employeeName}");
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 處理刪除員工請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_employee'])) {
    $employeeId = $_POST['employee_id'];
    $members = file('../data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $header = $members[0]; // 保存表頭
    $newMembers = [$header];
    
    foreach ($members as $index => $member) {
        if ($index > 0) { // 跳過表頭
            $fields = explode(' ', $member);
            if ($fields[0] !== $employeeId) {
                $newMembers[] = $member;
            }
        }
    }
    
    file_put_contents('../data/member.txt', implode("\n", $newMembers));
    $_SESSION['message'] = '員工資料已刪除';
    Logger::info("管理員 {$_SESSION['employee_name']} 刪除員工：{$employeeId}");
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 讀取所有員工資料
$members = file('../data/member.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$employees = [];
// 跳過表頭
for ($i = 1; $i < count($members); $i++) {
    $fields = explode(' ', $members[$i]);
    $employees[] = [
        'id' => $fields[0],
        'name' => $fields[1],
        'punch_password' => $fields[2],
        'admin_password' => $fields[3] ?? ''
    ];
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>員工管理 - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --secondary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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

        .card {
            background: var(--card-gradient);
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg), 0 0 20px rgba(99, 102, 241, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
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
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .employees-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
        }

        .employees-table th,
        .employees-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .employees-table th {
            background: rgba(99, 102, 241, 0.05);
            font-weight: 600;
            color: var(--text-primary);
        }

        .employees-table tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            animation: fadeIn 0.3s ease-out;
        }

        .message.success {
            background: var(--success-gradient);
            color: white;
        }

        .message.error {
            background: var(--danger-gradient);
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        @media (min-width: 769px) {
            .employee-cards {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .employees-table {
                display: none;
            }
            .employee-card {
                background: var(--card-gradient);
                border-radius: 1rem;
                box-shadow: var(--shadow-lg);
                padding: 1rem;
                margin-bottom: 1rem;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            .employee-card:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-lg), 0 0 20px rgba(99, 102, 241, 0.1);
            }
            .employee-card .card-header {
                font-weight: 600;
                color: var(--text-primary);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .employee-card .card-body {
                color: var(--text-secondary);
            }
            .employee-card .card-footer {
                display: flex;
                justify-content: flex-end;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users-cog"></i> 員工管理</h1>
            <p>管理員：<?php echo $_SESSION['employee_name']; ?></p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                <i class="fas fa-user-plus"></i> 新增員工
            </h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="employee_id"><i class="fas fa-id-card"></i> 員工編號</label>
                    <input type="text" id="employee_id" name="employee_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="employee_name"><i class="fas fa-user"></i> 員工姓名</label>
                    <input type="text" id="employee_name" name="employee_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="punch_password"><i class="fas fa-key"></i> 打卡密碼</label>
                    <input type="password" id="punch_password" name="punch_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="admin_password"><i class="fas fa-shield-alt"></i> 管理密碼（選填）</label>
                    <input type="password" id="admin_password" name="admin_password" class="form-control">
                </div>
                <button type="submit" name="add_employee" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 新增員工
                </button>
            </form>
        </div>

        <div class="card">
            <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                <i class="fas fa-users"></i> 員工列表
            </h3>
            <div style="overflow-x: auto;">
                <table class="employees-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-card"></i> 員工編號</th>
                            <th><i class="fas fa-user"></i> 員工姓名</th>
                            <th><i class="fas fa-key"></i> 打卡密碼</th>
                            <th><i class="fas fa-shield-alt"></i> 管理密碼</th>
                            <th><i class="fas fa-cog"></i> 操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['id']; ?></td>
                                <td><?php echo $employee['name']; ?></td>
                                <td><?php echo $employee['punch_password']; ?></td>
                                <td><?php echo $employee['admin_password'] ? '已設置' : '未設置'; ?></td>
                                <td>
                                    <form method="POST" action="" style="display: inline;" onsubmit="return confirm('確定要刪除此員工嗎？');">
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                        <button type="submit" name="delete_employee" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> 刪除
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- 手機端卡片樣式 -->
                <div class="employee-cards">
                    <?php foreach ($employees as $employee): ?>
                        <div class="employee-card">
                            <div class="card-header">
                                <span><i class="fas fa-id-card"></i> <?php echo $employee['id']; ?></span>
                                <span><i class="fas fa-user"></i> <?php echo $employee['name']; ?></span>
                            </div>
                            <div class="card-body">
                                <p><i class="fas fa-key"></i> 打卡密碼: <?php echo $employee['punch_password']; ?></p>
                                <p><i class="fas fa-shield-alt"></i> 管理密碼: <?php echo $employee['admin_password'] ? '已設置' : '未設置'; ?></p>
                            </div>
                            <div class="card-footer">
                                <form method="POST" action="" onsubmit="return confirm('確定要刪除此員工嗎？');">
                                    <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                    <button type="submit" name="delete_employee" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> 刪除
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="attendance_records.php" class="btn btn-primary">
                <i class="fas fa-clock"></i> 查看打卡記錄
            </a>
            <a href="../index.php" class="btn btn-secondary" style="margin-left: 1rem;">
                <i class="fas fa-home"></i> 返回首頁
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
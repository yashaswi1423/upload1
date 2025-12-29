<?php
// Admin Panel for Smart Horizon Hackathon
// View and manage problem statement submissions

session_start();

// Simple authentication - CHANGE THIS PASSWORD!
$admin_password = 'hackathon2026'; // Change this to a secure password

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check if logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Database connection
if ($is_logged_in) {
    try {
        require_once 'config/database_config.php';
        $pdo = getDBConnection();
    } catch (Exception $e) {
        $db_error = "Database connection failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Smart Horizon Hackathon</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .login-card, .admin-panel {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .login-card {
            max-width: 400px;
            margin: 100px auto;
            text-align: center;
        }
        
        .login-card h1 {
            color: #2b2d73;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2b2d73;
        }
        
        .btn {
            background: #2b2d73;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #1e1f4f;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .error {
            color: #dc3545;
            margin-top: 1rem;
            padding: 10px;
            background: #f8d7da;
            border-radius: 4px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e1e5e9;
        }
        
        .header h1 {
            color: #2b2d73;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #2b2d73, #c12d6b);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .submissions-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2b2d73;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .file-link {
            color: #2b2d73;
            text-decoration: none;
            font-weight: 500;
        }
        
        .file-link:hover {
            text-decoration: underline;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .admin-panel {
                padding: 1rem;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .submissions-table {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-card">
                <h1>🔐 Admin Login</h1>
                <p style="color: #666; margin-bottom: 2rem;">Smart Horizon Hackathon Admin Panel</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn">Login</button>
                    
                    <?php if (isset($login_error)): ?>
                        <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
                    <?php endif; ?>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Panel -->
            <div class="admin-panel">
                <div class="header">
                    <h1>📊 Admin Dashboard</h1>
                    <div>
                        <a href="index1.html" class="btn" style="margin-right: 10px;">🏠 Main Site</a>
                        <a href="?logout=1" class="btn btn-danger">Logout</a>
                    </div>
                </div>
                
                <?php if (isset($db_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($db_error); ?></div>
                <?php else: ?>
                    <?php
                    // Get statistics
                    try {
                        $total_stmt = $pdo->query("SELECT COUNT(*) FROM submissions");
                        $total_submissions = $total_stmt->fetchColumn();
                        
                        $pending_stmt = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'pending'");
                        $pending_submissions = $pending_stmt->fetchColumn();
                        
                        $approved_stmt = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'approved'");
                        $approved_submissions = $approved_stmt->fetchColumn();
                        
                        $rejected_stmt = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'rejected'");
                        $rejected_submissions = $rejected_stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo "<div class='error'>Error fetching statistics: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    ?>
                    
                    <!-- Statistics -->
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_submissions ?? 0; ?></div>
                            <div>Total Submissions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $pending_submissions ?? 0; ?></div>
                            <div>Pending Review</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $approved_submissions ?? 0; ?></div>
                            <div>Approved</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $rejected_submissions ?? 0; ?></div>
                            <div>Rejected</div>
                        </div>
                    </div>
                    
                    <!-- Submissions Table -->
                    <div class="submissions-table">
                        <h2 style="padding: 1rem; margin: 0; color: #2b2d73;">📝 Problem Statement Submissions</h2>
                        
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM submissions ORDER BY created_at DESC");
                            $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (empty($submissions)): ?>
                                <div class="no-data">
                                    <h3>No submissions yet</h3>
                                    <p>Submissions will appear here once participants start uploading their problem statements.</p>
                                </div>
                            <?php else: ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Organization</th>
                                            <th>SPOC Name</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Domain</th>
                                            <th>PS Title</th>
                                            <th>Logo</th>
                                            <th>Documents</th>
                                            <th>Status</th>
                                            <th>Submitted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['id']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['org_name']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['spoc_name']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['country_code'] . ' ' . $submission['spoc_contact']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['contact_email']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['domain'] ?: 'Not specified'); ?></td>
                                                <td><?php echo htmlspecialchars($submission['ps_title']); ?></td>
                                                <td>
                                                    <?php if ($submission['logo_path']): ?>
                                                        <a href="<?php echo htmlspecialchars($submission['logo_path']); ?>" target="_blank" class="file-link">View Logo</a>
                                                    <?php else: ?>
                                                        No logo
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($submission['documents_path']): ?>
                                                        <a href="<?php echo htmlspecialchars($submission['documents_path']); ?>" target="_blank" class="file-link">View Docs</a>
                                                    <?php else: ?>
                                                        No documents
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?php echo htmlspecialchars($submission['status']); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($submission['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y g:i A', strtotime($submission['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif;
                        } catch (PDOException $e) {
                            echo "<div class='error'>Error fetching submissions: " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
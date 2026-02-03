<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Clear any existing active session
unset($_SESSION['current_session_id']);
unset($_SESSION['question_count']);

// Check if coming from dashboard with preselected type
$preselect_type = $_GET['preselect'] ?? null;
$allowed_types = ['hr', 'technical', 'leetcode'];
$preselect_type = in_array($preselect_type, $allowed_types) ? $preselect_type : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Interview - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .selection-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .selection-step {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .selection-step h2 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .type-option {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .type-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .type-option.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1));
        }
        
        .type-option .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .type-option h3 {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        
        .type-option p {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .difficulty-options {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .difficulty-btn {
            padding: 1rem 2rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .difficulty-btn:hover {
            border-color: var(--primary-color);
        }
        
        .difficulty-btn.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }
        
        .difficulty-btn.easy { border-color: #10b981; }
        .difficulty-btn.easy.selected { background: #10b981; border-color: #10b981; }
        
        .difficulty-btn.medium { border-color: #f59e0b; }
        .difficulty-btn.medium.selected { background: #f59e0b; border-color: #f59e0b; }
        
        .difficulty-btn.hard { border-color: #ef4444; }
        .difficulty-btn.hard.selected { background: #ef4444; border-color: #ef4444; }
        
        .start-button {
            margin-top: 2rem;
            text-align: center;
        }
        
        .btn-start-interview {
            padding: 1rem 3rem;
            font-size: 1.1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-start-interview:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        
        .btn-start-interview:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 4px;
        }
        
        .info-box h4 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .info-box ul {
            margin-left: 1.5rem;
            color: var(--text-light);
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="logo-icon">🎯</span>
                <span class="logo-text">JobHandaAI</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><span class="icon">📊</span> Dashboard</a></li>
                <li><a href="start_interview.php" class="active"><span class="icon">💬</span> Start Interview</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="resume_builder.php"><span class="icon">📄</span> Resume Builder</a></li>
                <li><a href="resources.php"><span class="icon">📚</span> Resources</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Start New Interview Practice</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></div>
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                    </div>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <div class="content-area">
                <div class="selection-container">
                    <!-- Step 1: Select Interview Type -->
                    <div class="selection-step">
                        <h2>Step 1: Choose Interview Type</h2>
                        <div class="type-grid">
                            <div class="type-option" data-type="hr" onclick="selectType('hr')">
                                <div class="icon">💼</div>
                                <h3>HR Interview</h3>
                                <p>Practice behavioral and situational questions</p>
                            </div>
                            <div class="type-option" data-type="technical" onclick="selectType('technical')">
                                <div class="icon">⚙️</div>
                                <h3>Technical Interview</h3>
                                <p>Test your programming knowledge and concepts</p>
                            </div>
                            <div class="type-option" data-type="leetcode" onclick="selectType('leetcode')">
                                <div class="icon">🧩</div>
                                <h3>LeetCode Style</h3>
                                <p>Solve algorithmic coding problems</p>
                            </div>
                        </div>
                        
                        <div id="typeInfo" class="info-box" style="display: none;">
                            <h4 id="typeInfoTitle"></h4>
                            <ul id="typeInfoList"></ul>
                        </div>
                    </div>

                    <!-- Step 2: Select Difficulty -->
                    <div class="selection-step">
                        <h2>Step 2: Choose Difficulty Level</h2>
                        <div class="difficulty-options">
                            <button class="difficulty-btn easy" onclick="selectDifficulty('easy')">
                                <strong>🟢 Easy</strong>
                                <p>Beginner friendly</p>
                            </button>
                            <button class="difficulty-btn medium" onclick="selectDifficulty('medium')">
                                <strong>🟡 Medium</strong>
                                <p>Intermediate level</p>
                            </button>
                            <button class="difficulty-btn hard" onclick="selectDifficulty('hard')">
                                <strong>🔴 Hard</strong>
                                <p>Advanced questions</p>
                            </button>
                        </div>
                        
                        <div id="difficultyInfo" class="info-box" style="display: none;">
                            <h4 id="difficultyInfoTitle"></h4>
                            <p id="difficultyInfoText"></p>
                        </div>
                    </div>

                    <!-- Start Button -->
                    <div class="start-button">
                        <button id="startBtn" class="btn-start-interview" disabled onclick="startInterview()">
                            Select Type and Difficulty to Start
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let selectedType = <?php echo $preselect_type ? "'" . $preselect_type . "'" : 'null'; ?>;
        let selectedDifficulty = null;

        // Auto-select if preselected
        window.addEventListener('DOMContentLoaded', () => {
            if (selectedType) {
                selectType(selectedType);
            }
        });

        const typeInfo = {
            hr: {
                title: "HR Interview Questions",
                points: [
                    "Behavioral questions using STAR method",
                    "Conflict resolution scenarios",
                    "Leadership and teamwork experiences",
                    "Career goals and motivation",
                    "Strength and weakness discussions"
                ]
            },
            technical: {
                title: "Technical Interview Questions",
                points: [
                    "Programming concepts and paradigms",
                    "Data structures and algorithms",
                    "System design principles",
                    "Best practices and code quality",
                    "Debugging and problem-solving"
                ]
            },
            leetcode: {
                title: "LeetCode Style Problems",
                points: [
                    "Array and string manipulation",
                    "Graph and tree algorithms",
                    "Dynamic programming",
                    "Time and space complexity analysis",
                    "Optimal solution development"
                ]
            }
        };

        const difficultyInfo = {
            easy: {
                title: "Easy Level",
                text: "Perfect for beginners or warming up. Questions focus on fundamental concepts and basic scenarios."
            },
            medium: {
                title: "Medium Level",
                text: "Intermediate difficulty with more complex scenarios. Requires solid understanding and practical experience."
            },
            hard: {
                title: "Hard Level",
                text: "Advanced questions requiring deep knowledge, complex problem-solving, and detailed explanations."
            }
        };

        function selectType(type) {
            // Remove previous selection
            document.querySelectorAll('.type-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection
            document.querySelector(`[data-type="${type}"]`).classList.add('selected');
            selectedType = type;
            
            // Show info
            const info = typeInfo[type];
            document.getElementById('typeInfoTitle').textContent = info.title;
            const list = document.getElementById('typeInfoList');
            list.innerHTML = info.points.map(point => `<li>${point}</li>`).join('');
            document.getElementById('typeInfo').style.display = 'block';
            
            updateStartButton();
        }

        function selectDifficulty(difficulty) {
            // Remove previous selection
            document.querySelectorAll('.difficulty-btn').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection
            document.querySelector(`.difficulty-btn.${difficulty}`).classList.add('selected');
            selectedDifficulty = difficulty;
            
            // Show info
            const info = difficultyInfo[difficulty];
            document.getElementById('difficultyInfoTitle').textContent = info.title;
            document.getElementById('difficultyInfoText').textContent = info.text;
            document.getElementById('difficultyInfo').style.display = 'block';
            
            updateStartButton();
        }

        function updateStartButton() {
            const btn = document.getElementById('startBtn');
            if (selectedType && selectedDifficulty) {
                btn.disabled = false;
                btn.textContent = `Start ${selectedType.toUpperCase()} Interview (${selectedDifficulty.charAt(0).toUpperCase() + selectedDifficulty.slice(1)})`;
            } else {
                btn.disabled = true;
                btn.textContent = 'Select Type and Difficulty to Start';
            }
        }

        function startInterview() {
            if (selectedType && selectedDifficulty) {
                window.location.href = `interview.php?type=${selectedType}&difficulty=${selectedDifficulty}`;
            }
        }
    </script>
</body>
</html>

<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Resources - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .resources-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .resource-category {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .resource-category h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .resource-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .resource-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .resource-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }
        
        .resource-card h3 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .resource-card p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .resource-card ul {
            margin-left: 1.5rem;
            color: var(--text-light);
        }
        
        .resource-card ul li {
            margin-bottom: 0.5rem;
        }
        
        .company-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .company-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .company-badge:hover {
            transform: scale(1.05);
        }
        
        .tip-box {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        
        .accordion {
            margin-top: 1rem;
        }
        
        .accordion-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        
        .accordion-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }
        
        .accordion-header:hover {
            background: #e9ecef;
        }
        
        .accordion-header.active {
            background: var(--primary-color);
            color: white;
        }
        
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 1.5rem;
        }
        
        .accordion-content.active {
            max-height: 1000px;
            padding: 1.5rem;
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
                <li><a href="start_interview.php"><span class="icon">💬</span> Start Interview</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="resume_builder.php"><span class="icon">📄</span> Resume Builder</a></li>
                <li><a href="resources.php" class="active"><span class="icon">📚</span> Resources</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Interview Preparation Resources</h1>
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
                <div class="resources-container">
                    
                    <!-- HR Interview Resources -->
                    <div class="resource-category">
                        <h2>💼 HR Interview Preparation</h2>
                        <div class="resource-grid">
                            <div class="resource-card">
                                <h3>STAR Method</h3>
                                <p>Master the technique for answering behavioral questions</p>
                                <ul>
                                    <li><strong>S</strong>ituation: Set the context</li>
                                    <li><strong>T</strong>ask: Describe your responsibility</li>
                                    <li><strong>A</strong>ction: Explain what you did</li>
                                    <li><strong>R</strong>esult: Share the outcome</li>
                                </ul>
                                <div class="tip-box">
                                    💡 Always quantify results when possible (e.g., "increased sales by 25%")
                                </div>
                            </div>

                            <div class="resource-card">
                                <h3>Common Questions</h3>
                                <div class="accordion">
                                    <div class="accordion-item">
                                        <div class="accordion-header" onclick="toggleAccordion(this)">
                                            <span>Tell me about yourself</span>
                                            <span>+</span>
                                        </div>
                                        <div class="accordion-content">
                                            <p>Focus on: Professional background, key achievements, current goals, why you're interested in this role. Keep it under 2 minutes.</p>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" onclick="toggleAccordion(this)">
                                            <span>What's your greatest weakness?</span>
                                            <span>+</span>
                                        </div>
                                        <div class="accordion-content">
                                            <p>Choose a real weakness, explain steps you're taking to improve, and show self-awareness and growth mindset.</p>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" onclick="toggleAccordion(this)">
                                            <span>Describe a conflict situation</span>
                                            <span>+</span>
                                        </div>
                                        <div class="accordion-content">
                                            <p>Use STAR method. Show how you stayed professional, communicated effectively, found compromise, and maintained relationships.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="resource-card">
                                <h3>Body Language Tips</h3>
                                <ul>
                                    <li>Maintain good eye contact (60-70% of the time)</li>
                                    <li>Sit up straight but relaxed</li>
                                    <li>Use hand gestures naturally</li>
                                    <li>Smile genuinely when appropriate</li>
                                    <li>Mirror interviewer's energy subtly</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Interview Resources -->
                    <div class="resource-category">
                        <h2>⚙️ Technical Interview Preparation</h2>
                        <div class="resource-grid">
                            <div class="resource-card">
                                <h3>Data Structures</h3>
                                <p>Essential topics to master:</p>
                                <ul>
                                    <li>Arrays and Strings</li>
                                    <li>Linked Lists</li>
                                    <li>Stacks and Queues</li>
                                    <li>Trees and Graphs</li>
                                    <li>Hash Tables</li>
                                    <li>Heaps</li>
                                </ul>
                            </div>

                            <div class="resource-card">
                                <h3>Algorithms</h3>
                                <p>Key algorithm categories:</p>
                                <ul>
                                    <li>Searching (Binary Search, DFS, BFS)</li>
                                    <li>Sorting (Quick, Merge, Heap sort)</li>
                                    <li>Dynamic Programming</li>
                                    <li>Greedy Algorithms</li>
                                    <li>Recursion and Backtracking</li>
                                    <li>Two Pointers / Sliding Window</li>
                                </ul>
                            </div>

                            <div class="resource-card">
                                <h3>System Design</h3>
                                <p>For senior positions:</p>
                                <ul>
                                    <li>Scalability principles</li>
                                    <li>Load balancing</li>
                                    <li>Caching strategies</li>
                                    <li>Database design</li>
                                    <li>Microservices architecture</li>
                                    <li>CAP theorem</li>
                                </ul>
                                <div class="tip-box">
                                    💡 Always clarify requirements and constraints before designing
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LeetCode Resources -->
                    <div class="resource-category">
                        <h2>🧩 Coding Interview Strategy</h2>
                        <div class="resource-grid">
                            <div class="resource-card">
                                <h3>Problem-Solving Framework</h3>
                                <ol style="margin-left: 1.5rem; color: var(--text-light);">
                                    <li>Listen carefully and ask clarifying questions</li>
                                    <li>Work through an example</li>
                                    <li>State brute force approach</li>
                                    <li>Optimize (BUD: Bottlenecks, Unnecessary work, Duplicated work)</li>
                                    <li>Walk through your approach</li>
                                    <li>Write clean code</li>
                                    <li>Test your code</li>
                                </ol>
                            </div>

                            <div class="resource-card">
                                <h3>Time Complexity Cheat Sheet</h3>
                                <ul>
                                    <li>O(1) - Constant: Hash table lookup</li>
                                    <li>O(log n) - Logarithmic: Binary search</li>
                                    <li>O(n) - Linear: Simple loop</li>
                                    <li>O(n log n) - Linearithmic: Merge sort</li>
                                    <li>O(n²) - Quadratic: Nested loops</li>
                                    <li>O(2ⁿ) - Exponential: Recursive fibonacci</li>
                                </ul>
                            </div>

                            <div class="resource-card">
                                <h3>Common Patterns</h3>
                                <ul>
                                    <li><strong>Two Pointers:</strong> Sorted arrays, pairs</li>
                                    <li><strong>Sliding Window:</strong> Subarray problems</li>
                                    <li><strong>Fast & Slow Pointers:</strong> Cycle detection</li>
                                    <li><strong>Merge Intervals:</strong> Overlapping ranges</li>
                                    <li><strong>Top K Elements:</strong> Use heap</li>
                                    <li><strong>Modified Binary Search:</strong> Rotated arrays</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Company-Specific Prep -->
                    <div class="resource-category">
                        <h2>🏢 Company-Specific Preparation</h2>
                        <p>Popular companies and their interview focus:</p>
                        <div class="company-list">
                            <div class="company-badge" onclick="showCompanyInfo('google')">Google</div>
                            <div class="company-badge" onclick="showCompanyInfo('amazon')">Amazon</div>
                            <div class="company-badge" onclick="showCompanyInfo('microsoft')">Microsoft</div>
                            <div class="company-badge" onclick="showCompanyInfo('meta')">Meta</div>
                            <div class="company-badge" onclick="showCompanyInfo('apple')">Apple</div>
                            <div class="company-badge" onclick="showCompanyInfo('netflix')">Netflix</div>
                        </div>
                        <div id="companyDetails" style="margin-top: 2rem; display: none;">
                            <div class="resource-card">
                                <h3 id="companyName"></h3>
                                <div id="companyContent"></div>
                            </div>
                        </div>
                    </div>

                    <!-- General Tips -->
                    <div class="resource-category">
                        <h2>✨ General Interview Tips</h2>
                        <div class="resource-grid">
                            <div class="resource-card">
                                <h3>Before the Interview</h3>
                                <ul>
                                    <li>Research the company thoroughly</li>
                                    <li>Review the job description</li>
                                    <li>Prepare questions to ask</li>
                                    <li>Practice with mock interviews</li>
                                    <li>Test your tech setup (for virtual)</li>
                                    <li>Get good sleep the night before</li>
                                </ul>
                            </div>

                            <div class="resource-card">
                                <h3>During the Interview</h3>
                                <ul>
                                    <li>Arrive/login 5-10 minutes early</li>
                                    <li>Be enthusiastic and positive</li>
                                    <li>Listen actively to questions</li>
                                    <li>Think before you speak</li>
                                    <li>Ask for clarification if needed</li>
                                    <li>Show your problem-solving process</li>
                                </ul>
                            </div>

                            <div class="resource-card">
                                <h3>After the Interview</h3>
                                <ul>
                                    <li>Send thank-you email within 24 hours</li>
                                    <li>Reference specific conversation points</li>
                                    <li>Reiterate your interest</li>
                                    <li>Reflect on what went well/areas to improve</li>
                                    <li>Follow up if you don't hear back in the expected timeframe</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleAccordion(header) {
            const content = header.nextElementSibling;
            const symbol = header.querySelector('span:last-child');
            
            header.classList.toggle('active');
            content.classList.toggle('active');
            symbol.textContent = content.classList.contains('active') ? '−' : '+';
        }

        const companyData = {
            google: {
                name: "Google",
                content: `
                    <p><strong>Interview Style:</strong> Focus on data structures, algorithms, and system design.</p>
                    <ul>
                        <li>Emphasis on scalable solutions</li>
                        <li>Multiple rounds: phone screen, technical interviews, team matching</li>
                        <li>Behavioral questions about Googleyness</li>
                        <li>Prepare for coding on Google Docs or whiteboard</li>
                    </ul>
                    <p><strong>Key Values:</strong> Innovation, user focus, doing the right thing</p>
                `
            },
            amazon: {
                name: "Amazon",
                content: `
                    <p><strong>Interview Style:</strong> Heavy focus on Leadership Principles and behavioral questions.</p>
                    <ul>
                        <li>LP questions in every round (Customer Obsession, Ownership, etc.)</li>
                        <li>Use STAR method extensively</li>
                        <li>Technical: coding, system design, data structures</li>
                        <li>Bar raiser round - very thorough</li>
                    </ul>
                    <p><strong>Key Values:</strong> Customer obsession, ownership, bias for action</p>
                `
            },
            microsoft: {
                name: "Microsoft",
                content: `
                    <p><strong>Interview Style:</strong> Balanced technical and cultural fit assessment.</p>
                    <ul>
                        <li>Coding interviews with emphasis on problem-solving</li>
                        <li>System design for senior roles</li>
                        <li>Behavioral questions about teamwork and collaboration</li>
                        <li>As-appropriate round with multiple interviewers</li>
                    </ul>
                    <p><strong>Key Values:</strong> Growth mindset, collaboration, innovation</p>
                `
            },
            meta: {
                name: "Meta (Facebook)",
                content: `
                    <p><strong>Interview Style:</strong> Fast-paced coding and product design focus.</p>
                    <ul>
                        <li>Coding: 1-2 problems in 45 minutes</li>
                        <li>System design with product thinking</li>
                        <li>Behavioral: moving fast, impact focus</li>
                        <li>Live coding environment</li>
                    </ul>
                    <p><strong>Key Values:</strong> Move fast, focus on impact, be bold</p>
                `
            },
            apple: {
                name: "Apple",
                content: `
                    <p><strong>Interview Style:</strong> Product-focused with attention to detail.</p>
                    <ul>
                        <li>Deep technical knowledge in your domain</li>
                        <li>Past projects discussion in detail</li>
                        <li>Design principles and user experience thinking</li>
                        <li>Cultural fit with Apple values</li>
                    </ul>
                    <p><strong>Key Values:</strong> Innovation, excellence, attention to detail</p>
                `
            },
            netflix: {
                name: "Netflix",
                content: `
                    <p><strong>Interview Style:</strong> Culture-first with high bar for excellence.</p>
                    <ul>
                        <li>Context over control - demonstrate decision-making</li>
                        <li>Technical depth in your specialty</li>
                        <li>Show impact and ownership of projects</li>
                        <li>Culture memo alignment is crucial</li>
                    </ul>
                    <p><strong>Key Values:</strong> Freedom and responsibility, context not control</p>
                `
            }
        };

        function showCompanyInfo(company) {
            const details = document.getElementById('companyDetails');
            const name = document.getElementById('companyName');
            const content = document.getElementById('companyContent');
            
            name.textContent = companyData[company].name;
            content.innerHTML = companyData[company].content;
            details.style.display = 'block';
            
            // Scroll to details
            details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>
</body>
</html>

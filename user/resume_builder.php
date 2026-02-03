<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Get user's resume data if exists
$stmt = $conn->prepare("SELECT * FROM user_resumes WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Builder - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .resume-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .resume-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .resume-editor {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .resume-preview {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            margin-bottom: 1rem;
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-family: inherit;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .btn-analyze {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .analysis-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 1.5rem;
            margin-top: 1rem;
            border-radius: 6px;
        }
        
        .analysis-box h4 {
            color: #1e40af;
            margin-bottom: 1rem;
        }
        
        .score-meter {
            background: #e5e7eb;
            height: 30px;
            border-radius: 15px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .score-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-weight: bold;
        }
        
        @media (max-width: 968px) {
            .resume-grid {
                grid-template-columns: 1fr;
            }
            .resume-preview {
                position: static;
            }
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
                <li><a href="resume_builder.php" class="active"><span class="icon">📄</span> Resume Builder</a></li>
                <li><a href="resources.php"><span class="icon">📚</span> Resources</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>AI Resume Builder & Analyzer</h1>
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
                <div class="resume-container">
                    <div class="resume-grid">
                        <!-- Editor Section -->
                        <div class="resume-editor">
                            <h2>Build Your Resume</h2>
                            <p style="color: var(--text-light); margin-bottom: 2rem;">
                                Create a professional resume and get AI-powered feedback to improve it.
                            </p>

                            <form id="resumeForm">
                                <!-- Personal Information -->
                                <div class="form-section">
                                    <h3>Personal Information</h3>
                                    <div class="form-group">
                                        <label>Full Name *</label>
                                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($resume['full_name'] ?? $user['full_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($resume['email'] ?? $user['email']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($resume['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Professional Title *</label>
                                        <input type="text" name="title" placeholder="e.g., Software Engineer, Marketing Manager" value="<?php echo htmlspecialchars($resume['title'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="form-section">
                                    <h3>Professional Summary</h3>
                                    <div class="form-group">
                                        <label>Summary *</label>
                                        <textarea name="summary" placeholder="Write a brief professional summary (2-3 sentences)" required><?php echo htmlspecialchars($resume['summary'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <!-- Experience -->
                                <div class="form-section">
                                    <h3>Work Experience</h3>
                                    <div class="form-group">
                                        <label>Experience *</label>
                                        <textarea name="experience" placeholder="List your work experience with job title, company, dates, and key achievements" required><?php echo htmlspecialchars($resume['experience'] ?? ''); ?></textarea>
                                        <small>Tip: Use bullet points and quantify achievements when possible</small>
                                    </div>
                                </div>

                                <!-- Education -->
                                <div class="form-section">
                                    <h3>Education</h3>
                                    <div class="form-group">
                                        <label>Education *</label>
                                        <textarea name="education" placeholder="List your education: degree, institution, graduation year" required><?php echo htmlspecialchars($resume['education'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="form-section">
                                    <h3>Skills</h3>
                                    <div class="form-group">
                                        <label>Skills *</label>
                                        <textarea name="skills" placeholder="List your technical and soft skills (comma-separated)" required><?php echo htmlspecialchars($resume['skills'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn-action btn-submit-answer">Save Resume</button>
                                    <button type="button" onclick="analyzeResume()" class="btn-action btn-analyze">
                                        Analyze with AI
                                    </button>
                                </div>
                            </form>

                            <!-- Analysis Results -->
                            <div id="analysisResults" style="display: none;">
                                <div class="analysis-box">
                                    <h4>📊 Resume Analysis</h4>
                                    <div class="score-meter">
                                        <div class="score-fill" id="scoreFill" style="width: 0%">
                                            <span id="scoreText">0%</span>
                                        </div>
                                    </div>
                                    <div id="analysisContent"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="resume-preview">
                            <h3>Quick Tips</h3>
                            <div class="info-box">
                                <h4>✨ Resume Best Practices</h4>
                                <ul style="margin-left: 1.5rem; color: var(--text-light);">
                                    <li>Keep it to 1-2 pages maximum</li>
                                    <li>Use action verbs (achieved, led, developed)</li>
                                    <li>Quantify achievements with numbers</li>
                                    <li>Tailor your resume to the job description</li>
                                    <li>Proofread for spelling and grammar errors</li>
                                    <li>Use a clean, professional format</li>
                                    <li>Include relevant keywords from job postings</li>
                                </ul>
                            </div>

                            <div class="info-box" style="margin-top: 1rem;">
                                <h4>🎯 What Our AI Analyzes</h4>
                                <ul style="margin-left: 1.5rem; color: var(--text-light);">
                                    <li>Professional summary effectiveness</li>
                                    <li>Achievement quantification</li>
                                    <li>Skills relevance and presentation</li>
                                    <li>Overall structure and clarity</li>
                                    <li>ATS (Applicant Tracking System) compatibility</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('resumeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('api/save_resume.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Resume saved successfully!');
                } else {
                    alert('Error saving resume: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save resume');
            }
        });

        async function analyzeResume() {
            const formData = new FormData(document.getElementById('resumeForm'));
            const data = Object.fromEntries(formData.entries());
            
            // Show loading
            document.getElementById('analysisResults').style.display = 'block';
            document.getElementById('analysisContent').innerHTML = '<p>Analyzing your resume... This may take a moment.</p>';
            
            try {
                const response = await fetch('api/analyze_resume.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Animate score
                    const score = result.score;
                    const scoreFill = document.getElementById('scoreFill');
                    const scoreText = document.getElementById('scoreText');
                    
                    setTimeout(() => {
                        scoreFill.style.width = score + '%';
                        scoreText.textContent = score + '%';
                    }, 100);
                    
                    // Display analysis
                    document.getElementById('analysisContent').innerHTML = `
                        <div style="margin-top: 1rem;">
                            <h5>Strengths:</h5>
                            <p>${result.strengths}</p>
                            
                            <h5 style="margin-top: 1rem;">Areas for Improvement:</h5>
                            <p>${result.improvements}</p>
                            
                            <h5 style="margin-top: 1rem;">Recommendations:</h5>
                            <p>${result.recommendations}</p>
                        </div>
                    `;
                } else {
                    document.getElementById('analysisContent').innerHTML = 
                        '<p style="color: red;">Error analyzing resume. Please try again.</p>';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('analysisContent').innerHTML = 
                    '<p style="color: red;">Failed to analyze resume. Please try again.</p>';
            }
        }
    </script>
</body>
</html>

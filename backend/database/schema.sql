

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Careers table
CREATE TABLE careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    salary_range VARCHAR(100),
    skills JSON,
    growth_potential ENUM('Low', 'Medium', 'High', 'Very High'),
    demand ENUM('Low', 'Medium', 'High', 'Very High', 'Stable'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quiz results table
CREATE TABLE quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    answers JSON NOT NULL,
    recommendation VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Opportunities table
CREATE TABLE opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    company VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    type ENUM('internship', 'entry-level', 'scholarship', 'event') NOT NULL,
    salary VARCHAR(100),
    duration VARCHAR(100),
    deadline DATE,
    description TEXT,
    requirements JSON,
    remote BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Roadmap items table
CREATE TABLE roadmap_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    career_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    order_index INT DEFAULT 0,
    resources JSON,
    skills JSON,
    projects JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (career_id) REFERENCES careers(id) ON DELETE CASCADE
);

-- User progress table
CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    roadmap_item_id INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (roadmap_item_id) REFERENCES roadmap_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_item (user_id, roadmap_item_id)
);

-- Chat messages table
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT NOT NULL,
    is_bot BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample data

-- Careers
INSERT INTO careers (name, description, salary_range, skills, growth_potential, demand) VALUES
('Software Engineer', 'Build and maintain software applications and systems', '$70,000 - $180,000', '["Programming languages", "Frameworks", "Databases", "Problem solving"]', 'High', 'Very High'),
('Data Scientist', 'Analyze data to help organizations make better decisions', '$85,000 - $200,000', '["Statistics", "Machine Learning", "Python/R", "SQL"]', 'Very High', 'Very High'),
('UX Designer', 'Design user experiences for digital products', '$60,000 - $140,000', '["User Research", "Prototyping", "Design Tools", "User Empathy"]', 'High', 'High'),
('Product Manager', 'Bridge technology and business to deliver successful products', '$90,000 - $220,000', '["Strategic Thinking", "Communication", "Data Analysis", "Leadership"]', 'Very High', 'High'),
('Digital Marketer', 'Promote products and services through digital channels', '$45,000 - $100,000', '["SEO", "Social Media", "Content Creation", "Analytics"]', 'High', 'High'),
('Teacher', 'Educate and inspire the next generation', '$40,000 - $80,000', '["Communication", "Patience", "Curriculum Design", "Technology"]', 'Medium', 'Stable');

-- Opportunities
INSERT INTO opportunities (title, company, location, type, salary, duration, deadline, description, requirements, remote, featured) VALUES
('Software Engineering Internship', 'Google', 'Mountain View, CA', 'internship', '$8,000/month', '12 weeks', '2025-03-15', 'Join our team to build innovative products that impact billions of users worldwide.', '["Computer Science student", "Python/Java proficiency", "Data structures knowledge"]', FALSE, TRUE),
('UX Design Scholarship', 'Adobe Foundation', 'Worldwide', 'scholarship', '$10,000', NULL, '2025-04-01', 'Supporting underrepresented students pursuing careers in design and creativity.', '["Design portfolio", "Undergraduate student", "Financial need demonstration"]', TRUE, TRUE),
('Junior Data Analyst', 'Microsoft', 'Seattle, WA', 'entry-level', '$75,000 - $95,000', NULL, NULL, 'Analyze data to drive business decisions and create actionable insights.', '["Bachelor\'s degree", "SQL proficiency", "Excel/Power BI experience"]', TRUE, FALSE),
('AI Career Conference 2025', 'TechCareers', 'San Francisco, CA', 'event', NULL, '2 days', '2025-02-28', 'Network with industry professionals and learn about AI career opportunities.', '["Student ID", "Interest in AI/ML"]', FALSE, FALSE),
('Frontend Developer Internship', 'Netflix', 'Los Gatos, CA', 'internship', '$7,500/month', '10 weeks', '2025-03-20', 'Build user interfaces for our streaming platform used by millions globally.', '["JavaScript/React experience", "CSS/HTML proficiency", "Git knowledge"]', FALSE, FALSE),
('Product Management Fellowship', 'Amazon', 'Seattle, WA', 'internship', '$9,000/month', '16 weeks', '2025-03-10', 'Learn product management from industry leaders at Amazon.', '["Business/Engineering background", "Analytical skills", "Leadership experience"]', FALSE, TRUE);

-- Roadmap items for Software Engineering
INSERT INTO roadmap_items (career_id, title, description, level, order_index, resources, skills, projects) VALUES
(1, 'Programming Fundamentals', 'Learn the basics of programming with a beginner-friendly language', 'beginner', 1, '["Python for Everybody - Coursera (Free)", "Automate the Boring Stuff with Python", "CS50 Introduction to Computer Science"]', '["Variables & Data Types", "Control Structures", "Functions", "Basic Algorithms"]', '["Calculator App", "Number Guessing Game", "Simple Text Parser"]'),
(1, 'Web Development Basics', 'Build your first websites with HTML, CSS, and JavaScript', 'beginner', 2, '["The Complete Web Developer Bootcamp - Udemy", "freeCodeCamp Web Development", "HTML/CSS Challenges - Frontend Mentor"]', '["HTML5", "CSS3", "JavaScript ES6+", "Responsive Design"]', '["Personal Portfolio", "Landing Page", "Interactive Todo List"]'),
(1, 'Frontend Frameworks', 'Master modern frontend development with React or Vue', 'intermediate', 3, '["React Complete Guide - Udemy", "React Official Tutorial", "Build Real Apps - GitHub"]', '["React/Vue.js", "State Management", "Component Architecture", "API Integration"]', '["E-commerce Store", "Social Media Dashboard", "Weather App"]'),
(1, 'Backend Development', 'Learn server-side programming and database management', 'intermediate', 4, '["Node.js Complete Guide - Udemy", "Database Design - Khan Academy", "API Building Challenges - HackerRank"]', '["Node.js/Python", "Database Design", "RESTful APIs", "Authentication"]', '["Blog API", "Chat Application", "Task Management System"]'),
(1, 'Full-Stack Development', 'Combine frontend and backend to build complete applications', 'advanced', 5, '["Full-Stack Development Bootcamp - Coursera", "Capstone Project - Self-directed", "Open Source Contributions - GitHub"]', '["Full-Stack Architecture", "Deployment", "Performance Optimization", "Security"]', '["Complete Web Application", "Mobile App", "Real-time Application"]');

-- Roadmap items for Data Science
INSERT INTO roadmap_items (career_id, title, description, level, order_index, resources, skills, projects) VALUES
(2, 'Statistics Fundamentals', 'Learn the mathematical foundation of data science', 'beginner', 1, '["Statistics and Probability - Khan Academy", "Introduction to Statistics - Coursera", "Practical Statistics for Data Scientists"]', '["Descriptive Statistics", "Probability", "Hypothesis Testing", "Regression Analysis"]', '["Data Analysis Report", "Statistical Survey", "A/B Testing Project"]'),
(2, 'Programming for Data Science', 'Master Python and R for data analysis', 'beginner', 2, '["Python for Data Science - DataCamp", "R Programming - Coursera", "Pandas Tutorial"]', '["Python/R", "Pandas/NumPy", "Data Manipulation", "Data Visualization"]', '["Data Cleaning Script", "Exploratory Data Analysis", "Interactive Dashboard"]'),
(2, 'Machine Learning Basics', 'Introduction to machine learning algorithms', 'intermediate', 3, '["Machine Learning - Stanford", "Scikit-learn Tutorial", "Hands-On Machine Learning"]', '["Supervised Learning", "Unsupervised Learning", "Model Evaluation", "Feature Engineering"]', '["Classification Model", "Clustering Analysis", "Recommendation System"]'),
(2, 'Deep Learning', 'Advanced neural networks and deep learning', 'advanced', 4, '["Deep Learning Specialization - Coursera", "Fast.ai Course", "TensorFlow Tutorial"]', '["Neural Networks", "Convolutional Networks", "Recurrent Networks", "Transfer Learning"]', '["Image Classification", "Natural Language Processing", "Computer Vision Project"]'),
(2, 'Big Data and Cloud', 'Scale your data science projects', 'advanced', 5, '["Big Data with Spark - Coursera", "AWS Machine Learning", "Google Cloud ML"]', '["Distributed Computing", "Cloud Platforms", "Big Data Tools", "MLOps"]', '["Scalable ML Pipeline", "Real-time Analytics", "Production ML System"]');

-- Roadmap items for UX Design
INSERT INTO roadmap_items (career_id, title, description, level, order_index, resources, skills, projects) VALUES
(3, 'Design Fundamentals', 'Learn the principles of good design', 'beginner', 1, '["Design Principles - Canva", "Visual Design Fundamentals", "Color Theory Course"]', '["Visual Hierarchy", "Color Theory", "Typography", "Layout Design"]', '["Logo Design", "Poster Design", "Brand Identity"]'),
(3, 'User Research', 'Understand user needs and behaviors', 'beginner', 2, '["User Research Methods - Nielsen Norman", "UX Research Course", "Interview Techniques"]', '["User Interviews", "Surveys", "Usability Testing", "Persona Creation"]', '["User Research Report", "Persona Development", "Usability Study"]'),
(3, 'Prototyping and Wireframing', 'Create interactive prototypes', 'intermediate', 3, '["Figma Tutorial", "Sketch for Beginners", "Adobe XD Course"]', '["Wireframing", "Prototyping", "Design Systems", "Component Libraries"]', '["Mobile App Prototype", "Website Wireframes", "Design System"]'),
(3, 'Interaction Design', 'Design meaningful user interactions', 'intermediate', 4, '["Interaction Design Foundation", "Micro-interactions Course", "Animation Principles"]', '["Interaction Patterns", "Micro-interactions", "Animation", "Feedback Design"]', '["Interactive Prototype", "Animation Showcase", "Interaction Library"]'),
(3, 'UX Strategy and Leadership', 'Lead design teams and strategy', 'advanced', 5, '["UX Leadership Course", "Design Strategy Workshop", "Stakeholder Management"]', '["Design Strategy", "Team Leadership", "Stakeholder Communication", "Design Operations"]', '["Design Strategy Document", "Team Process", "Design System Implementation"]');

-- Create indexes for better performance
CREATE INDEX idx_quiz_results_user_id ON quiz_results(user_id);
CREATE INDEX idx_quiz_results_created_at ON quiz_results(created_at);
CREATE INDEX idx_opportunities_type ON opportunities(type);
CREATE INDEX idx_opportunities_featured ON opportunities(featured);
CREATE INDEX idx_roadmap_items_career_level ON roadmap_items(career_id, level);
CREATE INDEX idx_user_progress_user_item ON user_progress(user_id, roadmap_item_id);
CREATE INDEX idx_chat_messages_user_created ON chat_messages(user_id, created_at);

// Quiz Questions
const quizQuestions = [
    {
        question: "Which activities do you find most engaging?",
        options: [
            "Building and creating things",
            "Analyzing data and solving problems",
            "Helping and teaching others",
            "Designing and expressing creativity"
        ]
    },
    {
        question: "How do you prefer to work?",
        options: [
            "In a team with lots of collaboration",
            "Independently with minimal supervision",
            "Leading and managing others",
            "Following clear processes and guidelines"
        ]
    },
    {
        question: "What motivates you most in a career?",
        options: [
            "High salary and financial security",
            "Making a positive impact on society",
            "Continuous learning and growth",
            "Recognition and status"
        ]
    },
    {
        question: "Which skills come naturally to you?",
        options: [
            "Technical and analytical thinking",
            "Communication and interpersonal skills",
            "Creative and artistic abilities",
            "Leadership and organization"
        ]
    },
    {
        question: "What type of problems do you enjoy solving?",
        options: [
            "Technical challenges and debugging",
            "Human behavior and psychology",
            "Design and user experience",
            "Business strategy and optimization"
        ]
    }
];

// Chat Responses
const chatResponses = {
    "software engineer": "Software engineering is one of the fastest-growing fields with excellent job prospects. The average salary ranges from $70,000 to $180,000, with high demand for full-stack developers, mobile developers, and cloud engineers. Key skills include programming languages (Python, JavaScript, Java), frameworks, databases, and problem-solving abilities.",
    
    "data scientist": "Data science is experiencing tremendous growth as companies increasingly rely on data-driven decisions. Salaries range from $85,000 to $200,000. Essential skills include statistics, machine learning, Python/R, SQL, and data visualization. Industries like healthcare, finance, and tech are leading hiring.",
    
    "ux designer": "UX design is crucial as digital experiences become more important. Salaries range from $60,000 to $140,000. Key skills include user research, prototyping, design thinking, and tools like Figma, Sketch. Portfolio is extremely important in this field.",
    
    "product manager": "Product management is a growing field that bridges technology and business. Salaries range from $90,000 to $220,000. Important skills include strategic thinking, user empathy, data analysis, and communication. No specific degree required, but business or technical background helps.",
    
    "digital marketer": "Digital marketing continues to evolve with new platforms and technologies. Salaries range from $45,000 to $100,000. Key skills include SEO, social media marketing, content creation, analytics, and paid advertising. Certifications from Google, Facebook, and HubSpot are valuable.",
    
    "teacher": "Education remains a stable and rewarding career focused on impact rather than high salary. Salaries typically range from $40,000 to $80,000. Modern teachers need digital literacy, curriculum development skills, and adaptability. Online education is creating new opportunities."
};

// Global variables
let currentQuestionIndex = 0;
let quizAnswers = [];

// DOM Elements
const questionText = document.getElementById('questionText');
const optionsContainer = document.getElementById('optionsContainer');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const startQuizBtn = document.getElementById('startQuizBtn');
const exploreBtn = document.getElementById('exploreBtn');
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeQuiz();
    initializeChat();
    initializeNavigation();
    initializeAnimations();
});

// Quiz Functions
function initializeQuiz() {
    if (startQuizBtn) {
        startQuizBtn.addEventListener('click', startQuiz);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', previousQuestion);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', nextQuestion);
    }
    
    // Add event listeners to options
    optionsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('option')) {
            selectOption(e.target);
        }
    });
}

function startQuiz() {
    currentQuestionIndex = 0;
    quizAnswers = [];
    showQuestion();
    document.getElementById('quiz').scrollIntoView({ behavior: 'smooth' });
}

function showQuestion() {
    const question = quizQuestions[currentQuestionIndex];
    questionText.textContent = question.question;
    
    optionsContainer.innerHTML = '';
    question.options.forEach((option, index) => {
        const optionElement = document.createElement('div');
        optionElement.className = 'option';
        optionElement.setAttribute('data-value', index);
        optionElement.textContent = option;
        
        // Check if this option was previously selected
        if (quizAnswers[currentQuestionIndex] === index) {
            optionElement.classList.add('selected');
        }
        
        optionsContainer.appendChild(optionElement);
    });
    
    updateNavigationButtons();
}

function selectOption(optionElement) {
    // Remove selection from all options
    optionsContainer.querySelectorAll('.option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Select the clicked option
    optionElement.classList.add('selected');
    
    // Store the answer
    const selectedValue = parseInt(optionElement.getAttribute('data-value'));
    quizAnswers[currentQuestionIndex] = selectedValue;
}

function nextQuestion() {
    if (currentQuestionIndex < quizQuestions.length - 1) {
        currentQuestionIndex++;
        showQuestion();
    } else {
        completeQuiz();
    }
}

function previousQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showQuestion();
    }
}

function updateNavigationButtons() {
    prevBtn.disabled = currentQuestionIndex === 0;
    
    if (currentQuestionIndex === quizQuestions.length - 1) {
        nextBtn.textContent = 'Complete Quiz';
        nextBtn.innerHTML = 'Complete Quiz <i class="fas fa-check ms-1"></i>';
    } else {
        nextBtn.textContent = 'Next';
        nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right ms-1"></i>';
    }
}

function completeQuiz() {
    // Calculate career recommendation based on answers
    const recommendation = calculateCareerRecommendation();
    
    // Show results
    showQuizResults(recommendation);
}

function calculateCareerRecommendation() {
    // Simple career recommendation logic
    const careerScores = {
        'Software Engineer': 0,
        'Data Scientist': 0,
        'UX Designer': 0,
        'Product Manager': 0,
        'Digital Marketer': 0,
        'Teacher': 0
    };
    
    quizAnswers.forEach((answer, questionIndex) => {
        switch(questionIndex) {
            case 0: // Engaging activities
                if (answer === 0) careerScores['Software Engineer'] += 2;
                if (answer === 1) careerScores['Data Scientist'] += 2;
                if (answer === 2) careerScores['Teacher'] += 2;
                if (answer === 3) careerScores['UX Designer'] += 2;
                break;
            case 1: // Work preference
                if (answer === 0) careerScores['Product Manager'] += 2;
                if (answer === 1) careerScores['Software Engineer'] += 2;
                if (answer === 2) careerScores['Product Manager'] += 2;
                if (answer === 3) careerScores['Data Scientist'] += 2;
                break;
            case 2: // Motivation
                if (answer === 0) careerScores['Software Engineer'] += 1;
                if (answer === 1) careerScores['Teacher'] += 2;
                if (answer === 2) careerScores['Data Scientist'] += 2;
                if (answer === 3) careerScores['Product Manager'] += 1;
                break;
            case 3: // Natural skills
                if (answer === 0) careerScores['Software Engineer'] += 2;
                if (answer === 1) careerScores['Product Manager'] += 2;
                if (answer === 2) careerScores['UX Designer'] += 2;
                if (answer === 3) careerScores['Product Manager'] += 1;
                break;
            case 4: // Problem solving
                if (answer === 0) careerScores['Software Engineer'] += 2;
                if (answer === 1) careerScores['Data Scientist'] += 2;
                if (answer === 2) careerScores['UX Designer'] += 2;
                if (answer === 3) careerScores['Product Manager'] += 2;
                break;
        }
    });
    
    // Find the career with highest score
    let recommendedCareer = 'Software Engineer';
    let highestScore = 0;
    
    for (const [career, score] of Object.entries(careerScores)) {
        if (score > highestScore) {
            highestScore = score;
            recommendedCareer = career;
        }
    }
    
    return recommendedCareer;
}

function showQuizResults(career) {
    const quizCard = document.getElementById('quizCard');
    quizCard.innerHTML = `
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-trophy text-warning" style="font-size: 3rem;"></i>
            </div>
            <h3 class="mb-3">Your Career Recommendation</h3>
            <h2 class="text-primary mb-4">${career}</h2>
            <p class="lead mb-4">Based on your answers, we recommend pursuing a career in ${career.toLowerCase()}.</p>
            <div class="row">
                <div class="col-md-6">
                    <h5>Key Skills Needed:</h5>
                    <ul class="text-start">
                        <li>Technical expertise</li>
                        <li>Problem solving</li>
                        <li>Communication</li>
                        <li>Continuous learning</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Salary Range:</h5>
                    <p>$60,000 - $150,000</p>
                    <h5>Growth Potential:</h5>
                    <p>High demand with excellent opportunities</p>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary me-2" onclick="scrollToSection('roadmap')">
                    <i class="fas fa-map me-1"></i>
                    View Learning Roadmap
                </button>
                <button class="btn btn-outline-primary" onclick="scrollToSection('opportunities')">
                    <i class="fas fa-briefcase me-1"></i>
                    Browse Opportunities
                </button>
            </div>
        </div>
    `;
}

// Chat Functions
function initializeChat() {
    if (sendMessageBtn) {
        sendMessageBtn.addEventListener('click', sendMessage);
    }
    
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
}

function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) return;
    
    // Add user message
    addMessage(message, 'user');
    chatInput.value = '';
    
    // Generate and add bot response
    setTimeout(() => {
        const response = generateResponse(message);
        addMessage(response, 'bot');
    }, 1000);
}

function addMessage(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = text;
    
    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function generateResponse(userMessage) {
    const message = userMessage.toLowerCase();
    
    // Career-specific responses
    for (const [career, response] of Object.entries(chatResponses)) {
        if (message.includes(career.replace('_', ' ')) || message.includes(career)) {
            return response;
        }
    }
    
    // General responses
    if (message.includes('portfolio')) {
        return "Building a strong portfolio is crucial for your career success! Here are key tips:\n\n1. Quality over quantity - showcase 3-5 best projects\n2. Include case studies explaining your process\n3. Make it easy to navigate and mobile-friendly\n4. Keep it updated with recent work\n5. Include contact information and links to your work\n\nFor tech roles, GitHub is essential. For design roles, use Behance or a personal website. Would you like specific advice for your career path?";
    }
    
    if (message.includes('salary') || message.includes('pay')) {
        return "Salaries vary significantly based on location, experience, and industry. Here are some general ranges:\n\n• Software Engineer: $70k-$180k\n• Data Scientist: $85k-$200k\n• UX Designer: $60k-$140k\n• Product Manager: $90k-$220k\n• Digital Marketer: $45k-$100k\n\nRemember, salary isn't everything. Consider work-life balance, growth opportunities, company culture, and learning potential when evaluating opportunities.";
    }
    
    if (message.includes('skill') || message.includes('learn')) {
        return "The most important skills for career success include:\n\n• Technical skills specific to your field\n• Communication and teamwork\n• Problem-solving and critical thinking\n• Adaptability and continuous learning\n• Time management and organization\n\nFor specific career paths, focus on:\n- Software: Programming languages, frameworks, databases\n- Design: Design tools, user research, prototyping\n- Data Science: Statistics, machine learning, programming\n- Marketing: Analytics, content creation, digital platforms\n\nWhat career path are you most interested in?";
    }
    
    // Default response
    return "I'm here to help you with career guidance! You can ask me about:\n\n• Specific career paths and requirements\n• Salary information and job prospects\n• Building portfolios and skills\n• Learning resources and courses\n• Job search strategies\n\nWhat would you like to know more about?";
}

// Navigation Functions
function initializeNavigation() {
    if (exploreBtn) {
        exploreBtn.addEventListener('click', function() {
            scrollToSection('roadmap');
        });
    }
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

// Animation Functions
function initializeAnimations() {
    // Add fade-in animation to elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe all feature cards, roadmap items, and opportunity cards
    document.querySelectorAll('.feature-card, .roadmap-item, .opportunity-card').forEach(el => {
        observer.observe(el);
    });
}

// Utility Functions
function showLoading(element) {
    element.innerHTML = '<div class="loading-spinner"></div>';
}

function hideLoading(element, content) {
    element.innerHTML = content;
}

// Export functions for global access
window.scrollToSection = scrollToSection;
window.startQuiz = startQuiz;

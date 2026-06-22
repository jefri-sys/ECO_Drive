<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eco Drive - Community</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<script>
tailwind.config = {
theme: {
extend: {
colors: {
primary: '#2563eb',
secondary: '#64748b'
},
borderRadius: {
'none': '0px',
'sm': '4px',
DEFAULT: '8px',
'md': '12px',
'lg': '16px',
'xl': '20px',
'2xl': '24px',
'3xl': '32px',
'full': '9999px',
'button': '8px'
}
}
}
}
</script>
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
.modal {
display: none;
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background: rgba(0, 0, 0, 0.5);
z-index: 1000;
}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
<nav class="bg-white shadow-sm fixed top-0 left-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/S6 PROJECT(TEAM 6)/Mechanic/dashboard.php" class="text-gray-500 hover:text-primary">Dashboard</a>
                    <a href="#" class="text-gray-500 hover:text-primary">History</a>
                    <a href="/S6 PROJECT(TEAM 6)/Mechanic/inventory.php" class="text-gray-500 hover:text-primary">Inventory</a>
                    <a href="/S6 PROJECT(TEAM 6)/Mechanic/community_hub.php" class="text-primary font-medium">Community</a>
                    <div class="relative">
                        <button class="text-gray-600 hover:text-primary !rounded-button" id="notificationBtn">
                            <i class="ri-notification-3-line text-lg"></i>
                        </button>
                        <div class="absolute hidden bg-white shadow-lg rounded-lg w-80 right-0 mt-2 py-2 z-50" id="notificationDropdown">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <h3 class="font-medium">Notifications</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm text-gray-600">Your service request #SR2025021 has been accepted</p>
                                    <span class="text-xs text-gray-400">2 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button class="flex items-center space-x-2 cursor-pointer" id="profileBtn">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="ri-user-line text-gray-600"></i>
                            </div>
                            <span class="text-gray-700"><?php echo $_SESSION['username']; ?></span>
                            <i class="ri-arrow-down-s-line text-gray-600"></i>
                        </button>
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <p class="text-sm text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo $_SESSION['email']; ?></p>
                            </div>
                            <div class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="ri-user-line w-5 h-5 mr-3"></i>
                                    My Profile
                                </a>
                            </div>
                            <div class="py-2 border-t border-gray-100">
                                <a href="/S6 PROJECT(TEAM 6)/signout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <i class="ri-logout-box-line w-5 h-5 mr-3"></i>
                                    Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<main class="max-w-7xl mx-auto px-4 py-8 pt-20">
<div class="flex gap-8">
<div class="w-2/3">
<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-semibold text-gray-900">Discussion Forum</h2>
<button onclick="showNewPostModal()" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700 flex items-center">
<i class="ri-add-line mr-2"></i>New Post
</button>
</div>
<div class="mb-6">
<div class="relative">
<input type="text" id="searchPosts" placeholder="Search discussions..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
<i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
</div>
<div class="flex gap-4 mt-4">
<select id="topicFilter" class="bg-gray-50 border border-gray-200 rounded-button px-4 py-2 text-sm">
<option value="">All Topics</option>
<option value="Technical">Technical</option>
<option value="Tips">Tips & Tricks</option>
<option value="Discussion">Discussion</option>
<option value="Question">Questions</option>
</select>
<select id="sortPosts" class="bg-gray-50 border border-gray-200 rounded-button px-4 py-2 text-sm">
<option value="recent">Most Recent</option>
<option value="popular">Most Popular</option>
<option value="unanswered">Unanswered</option>
</select>
</div>
</div>
<div class="space-y-6" id="postsContainer"></div>
</div>

</div>
<div class="w-1/3 space-y-8">
<div class="bg-white rounded-lg shadow-sm p-6">
<div class="flex items-center space-x-4 mb-6">
<div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
<i class="ri-user-line text-3xl text-gray-400"></i>
</div>
<div>
<h3 class="text-lg font-medium text-gray-900">Alex Thompson</h3>
<p class="text-sm text-gray-500">Senior EV Mechanic</p>
</div>
</div>
<div class="space-y-4">
<div class="flex justify-between items-center">
<span class="text-gray-600">Reputation Score</span>
<span class="text-lg font-medium text-primary">4.8</span>
</div>
<div class="flex justify-between items-center">
<span class="text-gray-600">Posts</span>
<span>127</span>
</div>
<div class="flex justify-between items-center">
<span class="text-gray-600">Solutions</span>
<span>89</span>
</div>
</div>
<div class="mt-6 pt-6 border-t border-gray-100">
<h4 class="font-medium text-gray-900 mb-4">Badges</h4>
<div class="flex flex-wrap gap-2">
<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">EV Expert</span>
<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Top Contributor</span>
<span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Problem Solver</span>
</div>
</div>
</div>
<div class="bg-white rounded-lg shadow-sm p-6">
<h3 class="text-lg font-medium text-gray-900 mb-4">Trending Topics</h3>
<div class="space-y-4" id="trendingTopicsContainer"></div>
</div>
</div>
</div>
</main>
<div class="modal" id="newPostModal">
<div class="modal-content bg-white rounded-lg w-full max-w-2xl mx-auto mt-20 p-6">
<div class="flex justify-between items-center mb-6">
<h3 class="text-xl font-semibold text-gray-900">Create New Post</h3>
<button class="text-gray-400 hover:text-gray-600" onclick="closeNewPostModal()">
<i class="ri-close-line text-2xl"></i>
</button>
</div>
<form id="newPostForm" class="space-y-6">
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
<input type="text" required class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Topic</label>
<select required class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
<option value="Technical">Technical</option>
<option value="Tips">Tips & Tricks</option>
<option value="Discussion">Discussion</option>
<option value="Question">Question</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
<textarea required class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent h-32"></textarea>
</div>
<div class="flex justify-end space-x-4">
<button type="button" onclick="closeNewPostModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-button hover:bg-gray-200">Cancel</button>
<button type="submit" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700">Post</button>
</div>
</form>
</div>
</div>
<script>
        // Navigation Dropdowns
        const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    notificationBtn.addEventListener('click', () => {
        notificationDropdown.classList.toggle('hidden');
        profileDropdown.classList.add('hidden');
    });
    profileBtn.addEventListener('click', () => {
        profileDropdown.classList.toggle('hidden');
        notificationDropdown.classList.add('hidden');
    });
    document.addEventListener('click', (e) => {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
        if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });

const posts = [
{
id: 1,
title: "Best practices for EV battery maintenance during winter",
author: "Sarah Chen",
topic: "Technical",
content: "With winter approaching, I'd like to share some key tips for maintaining EV batteries in cold weather...",
likes: 45,
comments: 23,
timestamp: "2 hours ago"
},
{
id: 2,
title: "Troubleshooting regenerative braking systems",
author: "Marcus Rodriguez",
topic: "Question",
content: "I've noticed some inconsistencies in the regen braking on a Tesla Model 3. Here's what I've observed...",
likes: 32,
comments: 18,
timestamp: "4 hours ago"
},
{
id: 3,
title: "New certification course for EV charging systems",
author: "Emma Wilson",
topic: "Discussion",
content: "Just completed a new certification program for EV charging infrastructure. Here's my review...",
likes: 67,
comments: 34,
timestamp: "6 hours ago"
}
];

const trendingTopics = [
{
title: "New EV Motor Technology",
posts: 156,
trend: "up"
},
{
title: "Charging Standards",
posts: 98,
trend: "up"
},
{
title: "Battery Recycling",
posts: 87,
trend: "down"
}
];
function renderPosts() {
const container = document.getElementById('postsContainer');
container.innerHTML = posts.map(post => `
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
<div class="flex justify-between items-start mb-4">
<div>
<h3 class="text-lg font-medium text-gray-900">${post.title}</h3>
<p class="text-sm text-gray-500">Posted by ${post.author} · ${post.timestamp}</p>
</div>
<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${post.topic}</span>
</div>
<p class="text-gray-600 mb-4">${post.content}</p>
<div class="flex items-center space-x-6">
<button class="flex items-center space-x-2 text-gray-500 hover:text-primary">
<i class="ri-thumb-up-line"></i>
<span>${post.likes}</span>
</button>
<button class="flex items-center space-x-2 text-gray-500 hover:text-primary">
<i class="ri-chat-1-line"></i>
<span>${post.comments}</span>
</button>
</div>
</div>
`).join('');
}

function renderTrendingTopics() {
const container = document.getElementById('trendingTopicsContainer');
container.innerHTML = trendingTopics.map(topic => `
<div class="flex items-center justify-between">
<div class="flex items-center space-x-2">
<i class="ri-${topic.trend === 'up' ? 'arrow-up' : 'arrow-down'}-line text-${topic.trend === 'up' ? 'green' : 'red'}-500"></i>
<span class="text-gray-900">${topic.title}</span>
</div>
<span class="text-sm text-gray-500">${topic.posts} posts</span>
</div>
`).join('');
}
function showNewPostModal() {
document.getElementById('newPostModal').style.display = 'block';
}
function closeNewPostModal() {
document.getElementById('newPostModal').style.display = 'none';
}
document.getElementById('newPostForm').addEventListener('submit', function(e) {
e.preventDefault();
closeNewPostModal();
const successModal = document.createElement('div');
successModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center';
successModal.innerHTML = `
<div class="bg-white p-6 rounded-lg max-w-md w-full">
<h3 class="text-lg font-medium mb-4">Success</h3>
<p class="text-gray-600 mb-4">Your post has been published successfully.</p>
<button class="w-full px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700"
onclick="this.parentElement.parentElement.remove()">Close</button>
</div>
`;
document.body.appendChild(successModal);
});
document.addEventListener('DOMContentLoaded', function() {
renderPosts();
renderTrendingTopics();
});
</script>
</body>
</html>

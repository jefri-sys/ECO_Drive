<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}
$user_id = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Notification/notification_JS.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoDrive - Community</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { 
                extend: { 
                    colors: { primary: '#10B981', secondary: '#064E3B' }, 
                    borderRadius: { 'button': '8px' } 
                } 
            }
        };
    </script>
    <style>
        .post-card:hover { transform: translateY(-4px); transition: all 0.3s ease; }
        .tab-active { border-bottom: 2px solid #10B981; color: #10B981; }
    </style>
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow-sm fixed top-0 left-0 w-full">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                    <nav class="flex items-center space-x-6">
                        <a href="/S6 PROJECT(TEAM 6)/Customer/dashboard.php" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-home-line"></i><span>Dashboard</span>
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/dashboard.php#serviceRequest" onclick="showServiceRequestModal()" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-service-line"></i><span>Request Service</span>
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/request_details.php" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-settings-line mr-2"></i>My Services
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/community.php" class="text-primary font-medium">
                            <i class="ri-team-line"></i><span>Community</span>
                        </a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative cursor-pointer" id="notificationIcon" onclick="toggleNotifications()">
                        <i class="ri-notification-3-line text-gray-600 ri-lg"></i>
                        <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-medium">Notifications</h3>
                                <button onclick="markAllRead()" class="text-xs text-primary hover:underline">Mark all as read</button>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto"></div>
                            <div class="p-3 border-t border-gray-100 text-center">
                                <button onclick="loadMore()" class="text-sm text-primary hover:underline">Load more</button>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleUserMenu()">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="ri-user-line text-gray-600"></i>
                            </div>
                            <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="ri-arrow-down-s-line text-gray-600"></i>
                        </div>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <p class="text-sm text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            </div>
                            <div onclick="openProfileModal()" class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i  class="ri-user-line w-5 h-5 mr-3"></i>My Profile
                                </a>
                            </div>
                            <div class="py-2 border-t border-gray-100">
                                <a href="/S6 PROJECT(TEAM 6)/signout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <i class="ri-logout-box-line w-5 h-5 mr-3"></i>Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 pt-20 pb-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Community</h1>
            <div class="flex space-x-3">
                <button onclick="showAddPostModal()" class="bg-primary text-white px-4 py-2 rounded-button flex items-center space-x-2">
                    <i class="ri-add-line"></i><span>Add Post</span>
                </button>
                <button onclick="showAddReviewModal()" class="bg-white text-primary border border-primary px-4 py-2 rounded-button flex items-center space-x-2">
                    <i class="ri-star-line"></i><span>Add Review</span>
                </button>
            </div>
        </div>

        <div class="border-b border-gray-200 mb-6">
            <nav class="flex -mb-px">
                <button onclick="switchTab('posts')" id="postsTab" class="py-4 px-8 font-medium text-sm tab-active">Posts</button>
                <button onclick="switchTab('reviews')" id="reviewsTab" class="py-4 px-8 font-medium text-sm text-gray-500">Reviews</button>
            </nav>
        </div>

        <div id="postsContainer" class="space-y-6"></div>
        <div id="reviewsContainer" class="hidden space-y-6"></div>
        
        <div id="emptyState" class="text-center py-16 hidden">
            <p class="text-gray-500 text-lg">No posts yet. Be the first to share something!</p>
        </div>
    </main>

    <!-- Add Post Modal -->
    <div id="addPostModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Share a Post</h3>
                <button onclick="hideAddPostModal()" class="text-gray-500 hover:text-gray-700"><i class="ri-close-line ri-lg"></i></button>
            </div>
            <form id="addPostForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="postTitle" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea id="postContent" rows="4" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAddPostModal()" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Share Post</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Review Modal -->
    <div id="addReviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Write a Review</h3>
                <button onclick="hideAddReviewModal()" class="text-gray-500 hover:text-gray-700"><i class="ri-close-line ri-lg"></i></button>
            </div>
            <form id="addReviewForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Service Experience</label>
                    <select id="reviewType" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                        <option value="">Select the service you're reviewing</option>
                        <option value="Standard EV Care">Standard EV Care</option>
                        <option value="Fleet EV Plan">Fleet EV Plan</option>
                        <option value="Basic EV Maintenance">Basic EV Maintenance</option>
                        <option value="Premium EV Protection">Premium EV Protection</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rating</label>
                    <div class="flex items-center space-x-2 mt-1">
                        <div id="ratingStars" class="flex">
                            <button type="button" onclick="setRating(1)" class="text-gray-300 hover:text-yellow-400 text-2xl focus:outline-none">★</button>
                            <button type="button" onclick="setRating(2)" class="text-gray-300 hover:text-yellow-400 text-2xl focus:outline-none">★</button>
                            <button type="button" onclick="setRating(3)" class="text-gray-300 hover:text-yellow-400 text-2xl focus:outline-none">★</button>
                            <button type="button" onclick="setRating(4)" class="text-gray-300 hover:text-yellow-400 text-2xl focus:outline-none">★</button>
                            <button type="button" onclick="setRating(5)" class="text-gray-300 hover:text-yellow-400 text-2xl focus:outline-none">★</button>
                        </div>
                        <span id="ratingValue" class="text-sm font-medium ml-2">0/5</span>
                        <input type="hidden" id="reviewRating" name="rating" value="0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Review</label>
                    <textarea id="reviewContent" rows="4" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAddReviewModal()" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-checkbox-circle-line text-green-500 ri-2x"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="successMessage">Successfully shared!</h3>
            <p class="text-gray-600 mb-6" id="successSubMessage">Your content has been posted to the community.</p>
            <button onclick="hideSuccessModal()" class="w-full px-4 py-2 bg-primary text-white rounded-button">Done</button>
        </div>
    </div>

  <script>
    let currentTab = 'posts';
    let currentRating = 0;

    // Updated fetchData to consistently use JSON
    async function fetchData(action, data = {}) {
        try {
            const response = await fetch('community_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, ...data })
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return { success: false, error: error.message };
        }
    }

    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('postsTab').classList.toggle('tab-active', tab === 'posts');
        document.getElementById('postsTab').classList.toggle('text-gray-500', tab !== 'posts');
        document.getElementById('reviewsTab').classList.toggle('tab-active', tab === 'reviews');
        document.getElementById('reviewsTab').classList.toggle('text-gray-500', tab !== 'reviews');
        document.getElementById('postsContainer').classList.toggle('hidden', tab !== 'posts');
        document.getElementById('reviewsContainer').classList.toggle('hidden', tab !== 'reviews');
        tab === 'posts' ? loadPosts() : loadReviews();
    }

    async function loadPosts() {
        const result = await fetchData('get_posts');
        const container = document.getElementById('postsContainer');
        
        // Check if the response is successful and contains posts array
        if (!result.success || !Array.isArray(result.posts)) {
            console.error('Invalid posts data:', result);
            document.getElementById('emptyState').classList.remove('hidden');
            container.innerHTML = '';
            return;
        }
        
        document.getElementById('emptyState').classList.add('hidden');
        container.innerHTML = result.posts.map(post => `
            <div class="bg-white p-6 rounded-lg shadow-sm post-card">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="ri-user-line text-gray-600"></i>
                        </div>
                        <div>
                            <h3 class="font-medium">${escapeHtml(post.username)}</h3>
                            <p class="text-xs text-gray-500">${post.created_at}</p>
                        </div>
                    </div>
                    ${post.user_id === <?php echo $_SESSION['user_id']; ?> ? `
                        <button onclick="deletePost(${post.id})" class="text-red-500 hover:text-red-700" title="Delete post">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    ` : ''}
                </div>
                <h2 class="text-lg font-medium mt-4">${escapeHtml(post.title)}</h2>
                <p class="mt-2 text-gray-700">${escapeHtml(post.content)}</p>
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                    <button onclick="likePost(${post.id})" class="flex items-center space-x-1 ${post.user_liked ? 'text-primary' : 'text-gray-500'} hover:text-primary">
                        <i class="${post.user_liked ? 'ri-heart-fill' : 'ri-heart-line'}"></i>
                        <span>${post.likes_count} ${post.likes_count === 1 ? 'like' : 'likes'}</span>
                    </button>
                </div>
            </div>
        `).join('');
    }

    async function loadReviews() {
        const result = await fetchData('get_reviews');
        const container = document.getElementById('reviewsContainer');
        
        // Check if the response is successful and contains reviews array
        if (!result.success || !Array.isArray(result.reviews)) {
            console.error('Invalid reviews data:', result);
            document.getElementById('emptyState').classList.remove('hidden');
            container.innerHTML = '';
            return;
        }
        
        document.getElementById('emptyState').classList.add('hidden');
        container.innerHTML = result.reviews.map(review => `
            <div class="bg-white p-6 rounded-lg shadow-sm post-card">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="ri-user-line text-gray-600"></i>
                        </div>
                        <div>
                            <h3 class="font-medium">${escapeHtml(review.username)}</h3>
                            <p class="text-xs text-gray-500">${review.created_at}</p>
                        </div>
                    </div>
                    ${review.user_id === <?php echo $_SESSION['user_id']; ?> ? `
                        <button onclick="deleteReview(${review.id})" class="text-red-500 hover:text-red-700" title="Delete review">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    ` : ''}
                </div>
                <div class="flex items-center mt-3">
                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">${escapeHtml(review.service_type)}</span>
                    <div class="ml-4 flex text-yellow-400">
                        ${renderStars(review.rating)}
                    </div>
                </div>
                <p class="mt-3 text-gray-700">${escapeHtml(review.content)}</p>
            </div>
        `).join('');
    }

    function renderStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '★' : '☆';
            }
            return stars;
        }

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('reviewRating').value = rating;
        document.getElementById('ratingValue').textContent = `${rating}/5`;
        
        const stars = document.getElementById('ratingStars').children;
        for (let i = 0; i < stars.length; i++) {
            stars[i].classList.remove('text-gray-300', 'text-yellow-400');
            stars[i].classList.add(i < rating ? 'text-yellow-400' : 'text-gray-300');
        }
    }

    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '★' : '☆';
        }
        return stars;
    }

    function escapeHtml(unsafe) {
        return unsafe
            ?.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") || '';
    }

    async function likePost(postId) {
        const result = await fetchData('like_post', { post_id: postId });
        if (result.success) {
            loadPosts();
        } else {
            alert('Error: ' + result.error);
        }
    }

    async function deletePost(postId) {
        displayAlert(2, "Are you sure you want to delete this post?", null, postId, null, async (result) => {
            if (result.isConfirmed) {
                const deleteResult = await fetchData('delete_post', { post_id: postId });
                if (deleteResult.success) {
                    displayAlert(1, "Post deleted successfully", null);
                    loadPosts();
                } else {
                    displayAlert(3, "Error deleting post: " + (deleteResult.error || "Unknown error"), null);
                }
            }
        });
    }

    async function deleteReview(reviewId) {
        displayAlert(2, "Are you sure you want to delete this review?", null, reviewId, null, async (result) => {
            if (result.isConfirmed) {
                const deleteResult = await fetchData('delete_review', { review_id: reviewId });
                if (deleteResult.success) {
                    displayAlert(1, "Review deleted successfully", null);
                    loadReviews();
                } else {
                    displayAlert(3, "Error deleting review: " + (deleteResult.error || "Unknown error"), null);
                }
            }
        });
    }

    function showAddPostModal() {
            document.getElementById('addPostModal').style.display = 'flex';
            document.getElementById('postTitle').value = '';
            document.getElementById('postContent').value = '';
        }
        
        function hideAddPostModal() {
            document.getElementById('addPostModal').style.display = 'none';
        }
        
        function showAddReviewModal() {
            document.getElementById('addReviewModal').style.display = 'flex';
            document.getElementById('reviewType').value = '';
            document.getElementById('reviewContent').value = '';
            setRating(0);
        }
        
        function hideAddReviewModal() {
            document.getElementById('addReviewModal').style.display = 'none';
        }

        function showSuccessModal(message, subMessage) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successSubMessage').textContent = subMessage;
            document.getElementById('successModal').style.display = 'flex';
        }
        
        function hideSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
            document.addEventListener('click', function handler(event) {
                if (!event.target.closest('.cursor-pointer') && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', handler);
                }
            });
        }

    document.getElementById('addPostForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = {
            title: document.getElementById('postTitle').value,
            content: document.getElementById('postContent').value
        };
        const result = await fetchData('add_post', data);
        if (result.success) {
            hideAddPostModal();
            showSuccessModal('Post shared!', 'Your post has been shared with the community.');
            loadPosts();
        } else {
            alert('Error adding post: ' + (result.error || 'Unknown error'));
        }
    });

    document.getElementById('addReviewForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (currentRating === 0) {
            alert('Please select a rating');
            return;
        }
        const data = {
            service_type: document.getElementById('reviewType').value,
            rating: currentRating,
            content: document.getElementById('reviewContent').value
        };
        const result = await fetchData('add_review', data);
        if (result.success) {
            hideAddReviewModal();
            showSuccessModal('Review submitted!', 'Thank you for sharing your experience.');
            loadReviews();
        } else {
            alert('Error adding review: ' + (result.error || 'Unknown error'));
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadPosts();
    });
</script>
</body>
</html> 
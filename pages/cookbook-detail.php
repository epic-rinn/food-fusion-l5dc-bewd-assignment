<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/auth/session.php';

// Validate ID
$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: ' . SITE_URL . '/pages/community-cookbook.php');
    exit;
}

$pdo = get_db();

// Fetch cookbook + author
$stmt = $pdo->prepare(
    "SELECT c.*, u.name AS author_name
     FROM   cookbooks c
     LEFT   JOIN users u ON u.id = c.user_id
     WHERE  c.id = ?
     LIMIT  1"
);
$stmt->execute([$id]);
$cookbook = $stmt->fetch();

if (!$cookbook || $cookbook['status'] !== 'approved') {
    header('Location: ' . SITE_URL . '/pages/community-cookbook.php');
    exit;
}

// Check if current user has liked
$user_liked = false;
if (is_logged_in()) {
    $like_check = $pdo->prepare('SELECT id FROM cookbook_likes WHERE cookbook_id = ? AND user_id = ?');
    $like_check->execute([$id, current_user()['id']]);
    $user_liked = (bool)$like_check->fetch();
}

// Fetch comments
$comments_stmt = $pdo->prepare(
    'SELECT cc.*, u.name AS author_name
     FROM   cookbook_comments cc
     JOIN   users u ON u.id = cc.user_id
     WHERE  cc.cookbook_id = ?
     ORDER  BY cc.created_at ASC'
);
$comments_stmt->execute([$id]);
$comments = $comments_stmt->fetchAll();

// Related cookbooks — same country, exclude current, max 3
$related = [];
$rel_stmt = $pdo->prepare(
    "SELECT id, name, photo, country, cooking_type, total_likes
     FROM   cookbooks
     WHERE  country = ? AND id != ? AND status = 'approved'
     ORDER  BY total_likes DESC, created_at DESC
     LIMIT  3"
);
$rel_stmt->execute([$cookbook['country'], $id]);
$related = $rel_stmt->fetchAll();

// Meta
$page_title       = htmlspecialchars($cookbook['name']) . ' – Food Fusion';
$meta_description = substr(strip_tags($cookbook['description']), 0, 160);

require_once dirname(__DIR__) . '/includes/header.php';

$photo_src = $cookbook['photo']
    ? SITE_URL . '/uploads/cookbooks/' . htmlspecialchars($cookbook['photo'])
    : null;

// Split description into steps
$steps = array_values(array_filter(array_map('trim', explode("\n", $cookbook['description']))));
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 py-3 text-sm text-gray-400 flex items-center gap-2 flex-wrap">
        <a href="<?= SITE_URL ?>" class="hover:text-orange-500 transition">Home</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="<?= SITE_URL ?>/pages/community-cookbook.php" class="hover:text-orange-500 transition">Community Cookbook</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium truncate max-w-[200px]">
            <?= htmlspecialchars($cookbook['name']) ?>
        </span>
    </div>
</div>

<!-- Main content -->
<div class="max-w-5xl mx-auto px-4 py-12">

    <!-- Hero grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

        <!-- Photo -->
        <div class="rounded-3xl overflow-hidden shadow-lg bg-gray-100 aspect-[4/3]">
            <?php if ($photo_src): ?>
            <img src="<?= $photo_src ?>"
                 alt="<?= htmlspecialchars($cookbook['name']) ?>"
                 class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-3">
                <span class="text-7xl">📖</span>
                <span class="text-sm">No photo available</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info panel -->
        <div class="flex flex-col gap-5">

            <!-- Badges -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 bg-orange-100 text-orange-600 text-xs font-bold
                      px-3 py-1 rounded-full uppercase tracking-wide">
                    🌍 <?= htmlspecialchars($cookbook['country']) ?>
                </span>
                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-semibold
                      px-3 py-1.5 rounded-full">
                    🍳 <?= htmlspecialchars($cookbook['cooking_type']) ?>
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                <?= htmlspecialchars($cookbook['name']) ?>
            </h1>

            <!-- Like button -->
            <div class="flex items-center gap-4">
                <button id="like-btn"
                        onclick="toggleLike(<?= $cookbook['id'] ?>)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition
                               <?= $user_liked
                                   ? 'bg-red-50 text-red-500 border border-red-200 hover:bg-red-100'
                                   : 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-500' ?>">
                    <span id="like-icon"><?= $user_liked ? '❤️' : '🤍' ?></span>
                    <span id="like-count"><?= (int)$cookbook['total_likes'] ?></span>
                    <span id="like-text"><?= $user_liked ? 'Liked' : 'Like' ?></span>
                </button>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100"></div>

            <!-- Author & date -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center
                            text-white font-bold text-sm shrink-0 select-none">
                    <?= strtoupper(mb_substr($cookbook['author_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        <?= htmlspecialchars($cookbook['author_name'] ?? 'Unknown') ?>
                    </p>
                    <p class="text-xs text-gray-400">
                        Submitted <?= date('F j, Y', strtotime($cookbook['created_at'])) ?>
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3 pt-1">
                <a href="<?= SITE_URL ?>/pages/community-cookbook.php"
                   class="inline-flex items-center gap-2 border border-gray-200 text-gray-600
                          hover:border-orange-400 hover:text-orange-500 text-sm font-medium
                          px-5 py-2.5 rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    All Cookbooks
                </a>
                <?php if (!is_logged_in()): ?>
                <a href="<?= SITE_URL ?>/auth/login.php"
                   class="inline-flex items-center gap-2 bg-orange-500 text-white text-sm font-medium
                          px-5 py-2.5 rounded-full hover:bg-orange-600 transition">
                    Login to Like & Comment
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Steps -->
    <div class="mt-14 border-t border-gray-100 pt-10">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Step-by-Step Instructions</h2>
        <div class="space-y-4 max-w-2xl">
            <?php foreach ($steps as $i => $step): ?>
            <div class="flex gap-4 items-start">
                <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center
                            text-sm font-bold shrink-0 mt-0.5">
                    <?= $i + 1 ?>
                </div>
                <p class="text-gray-700 text-[15px] leading-relaxed pt-1">
                    <?= htmlspecialchars($step) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tips -->
    <?php if (!empty($cookbook['tips'])): ?>
    <div class="mt-10 bg-amber-50 border border-amber-200 rounded-2xl p-6 max-w-2xl">
        <h3 class="text-sm font-bold text-amber-700 uppercase tracking-wide mb-2">💡 Tips & Notes</h3>
        <p class="text-amber-800 text-sm leading-relaxed">
            <?= nl2br(htmlspecialchars($cookbook['tips'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Comments -->
    <div class="mt-14 border-t border-gray-100 pt-10 max-w-2xl">
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            Comments <span class="text-gray-400 text-base font-normal">(<?= count($comments) ?>)</span>
        </h2>

        <?php if (is_logged_in()): ?>
        <!-- Comment form -->
        <form id="comment-form" class="mb-8">
            <textarea id="comment-input" rows="3"
                      placeholder="Share your thoughts or experience with this recipe…"
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none
                             focus:ring-2 focus:ring-orange-300 transition resize-none"></textarea>
            <div class="flex justify-end mt-2">
                <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-2
                               rounded-full transition">
                    Post Comment
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-8 text-sm text-gray-500 text-center">
            <a href="<?= SITE_URL ?>/auth/login.php" class="text-orange-500 hover:underline font-medium">Login</a> to leave a comment.
        </div>
        <?php endif; ?>

        <!-- Comments list -->
        <div id="comments-list" class="space-y-5">
            <?php foreach ($comments as $cm): ?>
            <div class="flex gap-3 items-start">
                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center
                            text-gray-600 font-bold text-xs shrink-0 select-none">
                    <?= strtoupper(mb_substr($cm['author_name'], 0, 1)) ?>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($cm['author_name']) ?></span>
                        <span class="text-xs text-gray-400"><?= date('M j, Y · g:ia', strtotime($cm['created_at'])) ?></span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($cm['comment'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($comments)): ?>
        <p id="no-comments" class="text-sm text-gray-400 text-center py-6">No comments yet. Be the first to share your thoughts!</p>
        <?php endif; ?>
    </div>

    <!-- Related Cookbooks -->
    <?php if (!empty($related)): ?>
    <div class="mt-16 border-t border-gray-100 pt-12">
        <div class="flex items-center gap-3 mb-7">
            <h2 class="text-xl font-bold text-gray-900">
                More from <?= htmlspecialchars($cookbook['country']) ?>
            </h2>
            <div class="flex-1 h-px bg-gray-100"></div>
            <a href="<?= SITE_URL ?>/pages/community-cookbook.php?country=<?= urlencode($cookbook['country']) ?>"
               class="text-sm text-orange-500 hover:underline font-medium transition">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <?php foreach ($related as $rel):
                $rel_photo = $rel['photo']
                    ? SITE_URL . '/uploads/cookbooks/' . htmlspecialchars($rel['photo'])
                    : null;
            ?>
            <a href="<?= SITE_URL ?>/pages/cookbook-detail.php?id=<?= $rel['id'] ?>"
               class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200">
                <div class="aspect-video overflow-hidden bg-gray-100">
                    <?php if ($rel_photo): ?>
                    <img src="<?= $rel_photo ?>"
                         alt="<?= htmlspecialchars($rel['name']) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-4xl">📖</div>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 group-hover:text-orange-500 transition leading-snug">
                        <?= htmlspecialchars($rel['name']) ?>
                    </h3>
                    <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                        <span>🍳 <?= htmlspecialchars($rel['cooking_type']) ?></span>
                        <span>·</span>
                        <span>❤️ <?= (int)$rel['total_likes'] ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- AJAX scripts for like & comment -->
<script>
function toggleLike(cookbookId) {
    fetch('<?= SITE_URL ?>/auth/cookbooks/like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'cookbook_id=' + cookbookId
    })
    .then(r => {
        if (r.status === 401) {
            window.location.href = '<?= SITE_URL ?>/auth/login.php';
            return null;
        }
        return r.json();
    })
    .then(data => {
        if (!data) return;
        const btn   = document.getElementById('like-btn');
        const icon  = document.getElementById('like-icon');
        const count = document.getElementById('like-count');
        const text  = document.getElementById('like-text');

        count.textContent = data.total_likes;

        if (data.liked) {
            icon.textContent = '❤️';
            text.textContent = 'Liked';
            btn.className = btn.className
                .replace('bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-500',
                         'bg-red-50 text-red-500 border border-red-200 hover:bg-red-100');
        } else {
            icon.textContent = '🤍';
            text.textContent = 'Like';
            btn.className = btn.className
                .replace('bg-red-50 text-red-500 border border-red-200 hover:bg-red-100',
                         'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-500');
        }
    });
}

const commentForm = document.getElementById('comment-form');
if (commentForm) {
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('comment-input');
        const comment = input.value.trim();
        if (!comment) return;

        fetch('<?= SITE_URL ?>/auth/cookbooks/comment.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'cookbook_id=<?= $cookbook['id'] ?>&comment=' + encodeURIComponent(comment)
        })
        .then(r => {
            if (r.status === 401) {
                window.location.href = '<?= SITE_URL ?>/auth/login.php';
                return null;
            }
            return r.json();
        })
        .then(data => {
            if (!data || !data.success) return;

            // Remove "no comments" placeholder
            const noComments = document.getElementById('no-comments');
            if (noComments) noComments.remove();

            // Append new comment
            const list = document.getElementById('comments-list');
            const html = `
                <div class="flex gap-3 items-start">
                    <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center
                                text-gray-600 font-bold text-xs shrink-0 select-none">
                        ${data.initial}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-semibold text-gray-800">${data.author}</span>
                            <span class="text-xs text-gray-400">${data.created_at}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">${data.comment}</p>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
            input.value = '';
        });
    });
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

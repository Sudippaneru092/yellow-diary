<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}

$business_id = $_SESSION['business_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Image upload
    $imageName = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../admin/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $message = "❌ Failed to upload image.";
            $imageName = "";
        }
    }

    // Validate required fields
    if (empty($title) || empty($category) || empty($location) || empty($description) || empty($content)) {
        $message = "❌ Please fill in all required fields.";
    } else {
        $status = 'pending'; // Set status automatically to pending

        $stmt = $conn->prepare("INSERT INTO posts (business_id, title, category, location, description, content, image, status, created_at, views, featured) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0, ?)");

        $stmt->bind_param("isssssssi", $business_id, $title, $category, $location, $description, $content, $imageName, $status, $featured);

        if ($stmt->execute()) {
            $message = "✅ Post created successfully and is pending approval!";
        } else {
            $message = "❌ Error saving post. Please try again.";
        }

        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">📝 Create New Post</h2>

    <?php if (!empty($message)): ?>
        <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="text-end mb-3">
        <a href="manage_posts.php" class="btn btn-secondary">← Back to Manage Posts</a>
    </div>

    <form action="create_post.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Post Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category" id="category" class="form-control" required>
                <option value="">-- Select Category --</option>
                <option value="Event">Event</option>
                <option value="Job Vacancy">Job Vacancy</option>
                <option value="Announcement">Announcement</option>
                <option value="Advertising">Advertising</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" name="location" id="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Short Description <span class="text-danger">*</span></label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Full Content <span class="text-danger">*</span></label>
            <textarea name="content" id="content" class="form-control" rows="6" required></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" name="image" id="image" class="form-control-file">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="featured" id="featured">
            <label class="form-check-label" for="featured">Feature this post</label>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
        <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'Dashboard';
include 'students-layout-header.php';
include '_components/loading.php';
include '_components/sidebar.php';

$userId = $_SESSION['user_user_id']; // Add this line
// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $user_gender = $_POST['user_gender'];
    $grade_level = $_POST['grade_level'];

    // Handle file upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../upload/";
        $image_name = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . time() . '_' . $image_name;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
    } else {
        $target_file = $user['profile_image'] ?? null;
    }

    // Update user
    $stmt = $conn->prepare("UPDATE users SET user_name=?, user_gender=?, grade_level=?, profile_image=? WHERE user_id=?");
    $stmt->execute([$user_name, $user_gender, $grade_level, $target_file, $userId]);

    // Set a flag to show the success message
    $profileUpdated = true;
    
    // Refresh user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<section class="flex">
    <main class="flex-1 min-h-screen p-8 ml-[272px] max-[1023px]:ml-[0px] overflow-scroll-y">
        <button onclick="toggleSidebar()" class="lg:hidden text-primary rounded">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icon-tabler-menu">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 8l16 0" />
                <path d="M4 16l16 0" />
            </svg>
        </button>

        <div class="page-heading mb-6">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-600">Manage your profile information</p>
        </div>

        <?php if (isset($profileUpdated) && $profileUpdated): ?>
            <div id="success-message" class="p-4 mb-4 text-green-700 bg-green-100 rounded">Profile updated successfully!</div>
            <script>
                // Auto-hide the success message after 3 seconds
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6 max-w-xl">
            <div>
                <label class="block font-medium">Profile Image</label>
                <div class="flex items-center gap-4 mt-2">
                    <img src="<?= $user['profile_image'] ?? 'default-avatar.png' ?>" alt="Profile Image"
                         class="w-20 h-20 object-cover rounded-full border">
                    <input type="file" name="profile_image" accept="image/*" class="block w-full">
                </div>
            </div>

            <div>
                <label class="block font-medium">Full Name</label>
                <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>"
                       class="w-full mt-1 px-4 py-2 border rounded-lg" required>
            </div>

            <div>
                <label class="block font-medium">Gender</label>
                <select name="user_gender" class="w-full mt-1 px-4 py-2 border rounded-lg">
                    <option value="Male" <?= $user['user_gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $user['user_gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>

            <div>
                <label class="block font-medium">Grade Level</label>
                <input type="text" name="grade_level" value="<?= htmlspecialchars($user['grade_level']) ?>"
                       class="w-full mt-1 px-4 py-2 border rounded-lg" required>
            </div>

            <div>
                <label class="block font-medium text-gray-500">Access Level</label>
                <input type="text" value="<?= htmlspecialchars($user['access_level']) ?>" disabled
                       class="w-full mt-1 px-4 py-2 border rounded-lg bg-gray-100 text-gray-500">
            </div>

            <button type="submit"
                    class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition">Update Profile</button>
        </form>
    </main>
</section>

<?php include 'students-layout-footer.php'; ?>
<?php
    $pageTitle = 'Profile';
    include 'admin-layout-header.php';
    include '_components/loading.php';
    include '_components/sidebar.php';

    $userId = $_SESSION['user_id'] ?? null;
    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_name = $_POST['user_name'];
        $user_gender = $_POST['user_gender'];

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
        $stmt = $conn->prepare("UPDATE users SET user_name=?, user_gender=?, profile_image=? WHERE user_id=?");
        $stmt->execute([$user_name, $user_gender, $target_file, $userId]);

        // Set a flag to show the success message
        $profileUpdated = true;
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

    <section class="flex">
        <main class="flex-1 min-h-screen p-8 ml-[272px] max-[1023px]:ml-[0px] overflow-scroll-y relative">
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
                <div id="success-message" class="absolute p-4 mb-4 rounded z-999999999 w-full top-0 left-0">
                    <div class="p-4 mb-4 text-[#16904D] bg-[#D0E9DB]">Profile updated successfully!</div>
                </div>
                <script>
                    // Auto-hide the success message after 3 seconds
                    setTimeout(function() {
                        document.getElementById('success-message').style.display = 'none';
                    }, 3000);
                </script>
            <?php endif; ?>
            <div class="flex gap-10">
                <div class="w-[600px] max-[768px]:w-full">
                    <div class="flex flex-col items-center gap-[10px] p-5 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow relative">
                        <div class="w-full justify-items-center">
                            <img src="<?= $user['profile_image'] ?? 'default-avatar.png' ?>" alt="Profile Image"
                            class="w-[200px] h-[200px] object-cover rounded-full border">
                        </div>
                        <div class="w-full justify-items-center">
                            <h2 class="text-[25px] font-semibold mb-2 text-gray-800 text-center"><?= htmlspecialchars($user['user_name']) ?></h2>
                            <p class="text-[18px] text-gray-700 text-center neutral-grotesk">Administrator</p>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-[10px] p-5 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow relative">
                        <div class="w-full">
                            <label class="block font-medium">Profile Image</label>
                            <div class="flex items-center gap-4 mt-2">
                                <img src="<?= $user['profile_image'] ?? 'default-avatar.png' ?>" alt="Profile Image"
                                    class="w-20 h-20 object-cover rounded-full border">
                                <input type="file" name="profile_image" accept="image/*" class="block w-full">
                            </div>
                        </div>

                        <div class="w-full">
                            <label class="block font-medium">Full Name</label>
                            <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>"
                                class="w-full mt-1 px-4 py-2 border rounded-lg" required>
                        </div class="w-full">

                        <div class="w-full">
                            <label class="block font-medium">Gender</label>
                            <select name="user_gender" class="w-full mt-1 px-4 py-2 border rounded-lg">
                                <option value="Male" <?= $user['user_gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $user['user_gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>

                        <div class="w-full hidden">
                            <label class="block font-medium text-gray-500">Access Level</label>
                            <input type="text" value="<?= htmlspecialchars($user['access_level']) ?>" disabled
                                class="w-full mt-1 px-4 py-2 border rounded-lg bg-gray-100 text-gray-500">
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit"
                                class="bg-[#1d2a61] text-white !px-[15px] !py-[8px] rounded-[8px] text-sm">Update Profile</button>
                        </div>
                    </form> 
                </div>
            </div>
        </main>
    </section>

    <?php include 'admin-layout-footer.php'; ?>
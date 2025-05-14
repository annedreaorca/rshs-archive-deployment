<?php
$pageTitle = 'Edit Laboratory Equipment';
include 'admin-layout-header.php';
include '_components/loading.php';
include '../db-conn.php';

// Check if item_name is provided
if (isset($_GET['item_name'])) {
    $item_name = $_GET['item_name'];
    
    // Fetch item details
    $stmt = $conn->prepare("SELECT * FROM lab_equipments WHERE item_name = ?");
    $stmt->execute([$item_name]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        header("Location: inventory.php?error=Item not found");
        exit();
    }
    
    // Fetch all unique categories for dropdown
    $sqlCategories = "SELECT DISTINCT category FROM lab_equipments WHERE category IS NOT NULL AND category != '' ORDER BY category";
    $stmtClass = $conn->prepare($sqlCategories);
    $stmtClass->execute();
    $categories = $stmtClass->fetchAll(PDO::FETCH_ASSOC);

    // Predefined categories if database doesn't have any yet
    $predefinedCategories = [
        'Mechanics',
        'Electricity & Electronics',
        'Magnetism & Electromagnetism',
        'Waves & Sound',
        'Optics & Light',
        'Thermodynamics',
        'Fluid Mechanics',
        'General Chemistry',
        'Electrochemistry',
        'Specialized Chemistry',
        'Biology & Life Sciences',
        'Environmental Science',
        'Engineering & Demonstration Models',
        'Educational Materials',
        'General Lab Supplies'
    ];
    
    // Merge existing and predefined categories (removing duplicates)
    $allCategories = [];
    foreach ($categories as $category) {
        $allCategories[] = $category['category'];
    }
    
    $finalCategories = array_unique(array_merge($allCategories, $predefinedCategories));
    sort($finalCategories); // Sort alphabetically
} else {
    header("Location: inventory.php?error=No item specified");
    exit();
}
?>

<section class="flex">
    <?php include '_components/sidebar.php'; ?>
    <main class="flex-1 min-h-screen p-8 ml-[272px] max-[1023px]:ml-[0px] overflow-scroll-y">
        <div class="page-heading">
            <h1>Edit Laboratory Equipment</h1>
            <p class="text-gray-600">Update information for "<?= htmlspecialchars($item_name) ?>"</p>
        </div>
        <?php if (isset($_GET['prompt']) && $_GET['prompt'] == 'edit'): ?>
            <div class="bg-yellow-100 text-yellow-800 p-4 my-4 rounded-md">
                This item already exists. You can edit its information below.
            </div>
        <?php endif; ?>
        <div class="flex flex-col mt-10 bg-white shadow-lg rounded-lg p-6">
            <form class="space-y-6" action="add-item-function.php" method="post" enctype="multipart/form-data">
                <!-- Item Name (readonly) -->
                <div>
                    <label for="item_name" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="item_name" id="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 bg-gray-100" readonly>
                </div>

                <!-- Item Description -->
                <div>
                    <label for="item_description" class="block text-sm font-medium text-gray-700">Item Description</label>
                    <input type="text" name="item_description" id="item_description" value="<?= htmlspecialchars($item['item_description']) ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                </div>

                <!-- Category Dropdown -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($finalCategories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= ($item['category'] == $category) ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Custom Category Input (shows when "Other" is selected) -->
                <div id="customCategoryContainer" style="display: none;">
                    <label for="custom_category" class="block text-sm font-medium text-gray-700">Custom Category</label>
                    <input type="text" name="custom_category" id="custom_category" placeholder="Enter custom category" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                </div>

                <!-- Item Status -->
                <div>
                    <label for="item_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="item_status" id="item_status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                        <option value="Available" <?= ($item['item_status'] == 'Available') ? 'selected' : '' ?>>Available</option>
                        <option value="Not Available" <?= ($item['item_status'] == 'Not Available') ? 'selected' : '' ?>>Not Available</option>
                    </select>
                </div>

                <!-- Availability -->
                <div>
                    <label for="total_available" class="block text-sm font-medium text-gray-700">Number of Items</label>
                    <input type="number" name="total_available" id="total_available" value="<?= htmlspecialchars($item['total_available']) ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                </div>

                <!-- Current Image Preview -->
                <?php if (!empty($item['temp_name'])): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Image</label>
                    <img src="../upload/<?= htmlspecialchars($item['temp_name']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="mt-2 w-60 h-auto object-cover rounded-lg shadow-md" onerror="this.onerror=null; this.src='../assets/images/placeholder-broken-image.jpg';">
                </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <a href="inventory.php" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Cancel</a>
                    <button type="submit" name="update" class="button-primary">Update Item</button>
                </div>
            </form>
        </div>
    </main>
</section>

<?php include 'admin-layout-footer.php'; ?>

<script>
window.addEventListener('DOMContentLoaded', (event) => {
    const categorySelect = document.getElementById('category');
    const customcategoryContainer = document.getElementById('customCategoryContainer');
    
    // Add "Other" option at the end of the dropdown
    const otherOption = document.createElement('option');
    otherOption.value = "other";
    otherOption.textContent = "Other (Custom)";
    categorySelect.appendChild(otherOption);
    
    // Check if the current category is not in the list, select "Other"
    const currentCategory = "<?= addslashes($item['category'] ?? '') ?>";
    if (currentCategory && !Array.from(categorySelect.options).some(option => option.value === currentCategory)) {
        categorySelect.value = "other";
        document.getElementById('custom_category').value = currentCategory;
        customCategoryContainer.style.display = 'block';
    }
    
    // Show/hide custom category input based on selection
    categorySelect.addEventListener('change', function() {
        if (this.value === 'other') {
            customCategoryContainer.style.display = 'block';
        } else {
            customCategoryContainer.style.display = 'none';
        }
    });
});
</script>
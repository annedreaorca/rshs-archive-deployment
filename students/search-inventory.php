<?php
include '../db-conn.php';

// Set base URL for redirections
$base_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = "/rshs-archive";
}
if (isset($_GET['query'])) {
    $searchQuery = '%' . $_GET['query'] . '%';

    // Search for items that match the query
    $sql = "SELECT item_id, item_name, item_description, temp_name, item_status, total_available FROM lab_equipments WHERE item_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$searchQuery]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        foreach ($results as $lab_equipment) {
            echo '<div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow relative">';
            echo '<div class="p-6">';
            echo '<div class="relative pt-5">';
            echo '<img src="' . $base_url . '/upload/' . htmlspecialchars($lab_equipment['temp_name']) . '" 
                    alt="' . htmlspecialchars($lab_equipment['item_name']) . '" 
                    class="w-full h-70 object-cover rounded-lg mb-4" 
                    onerror="this.onerror=null; this.src=\'' . $base_url . '/assets/images/placeholder-broken-image.jpg\'">';
            echo '<div class="absolute top-0 right-0">';
            
            // Availability badge
            $availabilityClass = ($lab_equipment['total_available'] > 0) ? 'flex justify-center bg-[#D0E9DB] text-[#16904D] py-2 px-5 rounded-[5px] neural-grotesk text-[13px]' : 'flex justify-center bg-[#F0D2D2] text-[#B61E1E] py-2 px-5 rounded-[5px] neural-grotesk text-[13px]';
            $availabilityText = ($lab_equipment['total_available'] > 0) ? 'Available' : 'No Available';
            
            echo '<span class="' . $availabilityClass . '">' . $availabilityText . '</span>';
            echo '</div>';
            echo '</div>';
            
            echo '<h2 class="text-xl font-semibold mb-2 text-gray-800">' . htmlspecialchars($lab_equipment['item_name']) . '</h2>';
            echo '<p class="text-[14px] text-gray-700">' . htmlspecialchars($lab_equipment['item_description']) . '</p>';
            echo '<div class="h-[50px] spacer"></div>';
            echo '</div>';
            
            echo '<div class="flex items-center justify-between absolute bottom-0 border-t border-t-1 w-full px-6 py-4">';
            echo '<div>';
            echo '<p class="text-sm text-gray-700"><strong>Availability</strong> <span class="text-zinc-400 px-1">-</span> ' . htmlspecialchars($lab_equipment['total_available']) . '</p>';
            echo '</div>';
            echo '<div>';
            echo '<a href="borrow-form.php?id=' . urlencode($lab_equipment['item_id']) . '" 
                  class="button-primary text-[12px] pt-[7px] pb-[9px] px-4" 
                  onclick="return confirm(\'Are you sure you want to borrow this item?\');">Borrow</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-gray-600 text-lg'>No items found.</p>";
    }
}
?>
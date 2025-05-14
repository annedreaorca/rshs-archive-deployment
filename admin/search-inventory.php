<?php
include '../db-conn.php';

if (isset($_GET['query'])) {
    $searchQuery = '%' . $_GET['query'] . '%';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    
    // Base SQL with search term
    $sql = "SELECT item_name, item_description, temp_name, item_status, total_available, category 
            FROM lab_equipments 
            WHERE item_name LIKE :search";
    
    $params = [':search' => $searchQuery];
    
    // Add category filter if provided
    if (!empty($category)) {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        foreach ($results as $lab_equipment) {
            echo '<div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow relative">';
            echo '<div class="p-6">';
            echo '<div class="relative">';
            echo '<img src="../upload/' . htmlspecialchars($lab_equipment['temp_name']) . '" 
                    alt="' . htmlspecialchars($lab_equipment['item_name']) . '" 
                    class="w-full h-70 object-cover rounded-lg mb-4 mt-5" 
                    onerror="this.onerror=null; this.src=\'/assets/images/placeholder-broken-image.jpg\'">';
            echo '<div class="absolute top-0 right-0">';
            
            // Corrected class rendering
            $availabilityClass = ($lab_equipment['total_available'] > 0) ? 'flex justify-center bg-[#D0E9DB] text-[#16904D] py-2 px-5 rounded-[5px] neural-grotesk text-[13px]' : 'flex justify-center bg-[#F0D2D2] text-[#B61E1E] py-2 px-5 rounded-[5px] neural-grotesk text-[13px]';
            $availabilityText = ($lab_equipment['total_available'] > 0) ? 'Available' : 'No Available';
            
            echo '<span class="' . $availabilityClass . '">' . $availabilityText . '</span>';
            echo '</div>';
            echo '</div>';
            
            echo '<h2 class="text-xl font-semibold mb-2 text-gray-800">' . htmlspecialchars($lab_equipment['item_name']) . '</h2>';
            
            // Display category badge
            if (!empty($lab_equipment['category'])) {
                echo '<div class="mb-2">';
                echo '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">' . htmlspecialchars($lab_equipment['category']) . '</span>';
                echo '</div>';
            }
            
            echo '<p class="text-[14px] text-gray-700">' . htmlspecialchars($lab_equipment['item_description']) . '</p>';
            echo '<div class="h-[50px] spacer"></div>';
            echo '</div>';
            
            echo '<div class="flex items-center justify-between absolute bottom-0 border-t border-t-1 w-full px-6 py-4">';
            echo '<div>';
            echo '<p class="text-sm text-gray-700"><strong>Availability</strong> <span class="text-zinc-400 px-1">-</span> ' . htmlspecialchars($lab_equipment['total_available']) . '</p>';
            echo '</div>';
            echo '<div>';
            echo '<a href="edit-item.php?item_name=' . urlencode($lab_equipment['item_name']) . '" class="button-primary text-[12px] pt-[7px] pb-[9px] px-4">Edit</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-gray-600 text-lg'>No items found matching your search criteria.</p>";
    }
}
?>
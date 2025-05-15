<?php 
$pageTitle = 'Dashboard';
include 'students-layout-header.php';
include '_components/loading.php';
include '../db-conn.php';

// Updated to use the correct session variable name
$current_user = $_SESSION['user_lrn_or_email'] ?? '';

// Fetch total items borrowed by current user
$sql = "SELECT SUM(quantity) FROM borrowed_items WHERE lrn_or_email = ? AND status = 'Approved' AND date_returned IS NULL";
$result = $conn->prepare($sql);
$result->execute([$current_user]);
$total_my_borrowed_items = $result->fetchColumn() ?: 0;

// Fetch total available equipment
$sql = "SELECT SUM(total_available) FROM lab_equipments WHERE is_archived = 0";
$result = $conn->prepare($sql);
$result->execute();
$total_available_equipments = $result->fetchColumn() ?: 0;

// Fetch total pending requests for current user
$sql = "SELECT COUNT(*) FROM borrowed_items WHERE lrn_or_email = ? AND status = 'Approved'";
$result = $conn->prepare($sql);
$result->execute([$current_user]);
$total_my_pending_requests = $result->fetchColumn() ?: 0;

// Fetch total returned items by current user
$sql = "SELECT COUNT(*) FROM borrowed_items WHERE lrn_or_email = ? AND date_returned IS NOT NULL";
$result = $conn->prepare($sql);
$result->execute([$current_user]);
$total_my_returned_items = $result->fetchColumn() ?: 0;

// Fetch current user's recent borrowed items
$sql = "SELECT e.item_name, b.quantity, b.date_borrowed, b.status
        FROM borrowed_items b
        JOIN lab_equipments e ON b.item_id = e.item_id
        WHERE b.lrn_or_email = ?
        ORDER BY b.date_borrowed DESC
        LIMIT 10";
$result = $conn->prepare($sql);
$result->execute([$current_user]);
$my_borrowed_items = $result->fetchAll(PDO::FETCH_ASSOC);

// Fetch available equipment list
$sql = "SELECT item_name, item_description, total_available, category
        FROM lab_equipments
        WHERE is_archived = 0 AND total_available > 0
        ORDER BY item_name ASC
        LIMIT 10";
$result = $conn->prepare($sql);
$result->execute();
$available_equipment = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="flex">
    <?php include '_components/sidebar.php';?>
    <main class="flex-1 min-h-screen p-8 ml-[272px] max-[1023px]:ml-[0px] overflow-scroll-y">
        <button onclick="toggleSidebar()" class="lg:hidden text-primary rounded">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8l16 0"/><path d="M4 16l16 0"/>
            </svg>
        </button>
        
        <div class="page-heading">
            <h1>Student Dashboard</h1>
            <p class="text-gray-600">Manage your lab equipment requests with ease.</p>
        </div>

        <div class="flex gap-[20px] mt-10 max-[1320px]:flex-col">
            <div class="flex flex-col gap-5 w-6/12 max-[1320px]:w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 bg-white shadow-md rounded-lg overview-cards p-2">
                    <div class="card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cube"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 16.008v-8.018a1.98 1.98 0 0 0 -1 -1.717l-7 -4.008a2.016 2.016 0 0 0 -2 0l-7 4.008c-.619 .355 -1 1.01 -1 1.718v8.018c0 .709 .381 1.363 1 1.717l7 4.008a2.016 2.016 0 0 0 2 0l7 -4.008c.619 -.355 1 -1.01 1 -1.718z" /><path d="M12 22v-10" /><path d="M12 12l8.73 -5.04" /><path d="M3.27 6.96l8.73 5.04" />
                            </svg>
                        </div>
                        <div class="card-content">
                            <h2>My Borrowed Items</h2>
                            <span><?= $total_my_borrowed_items; ?></span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cube"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 16.008v-8.018a1.98 1.98 0 0 0 -1 -1.717l-7 -4.008a2.016 2.016 0 0 0 -2 0l-7 4.008c-.619 .355 -1 1.01 -1 1.718v8.018c0 .709 .381 1.363 1 1.717l7 4.008a2.016 2.016 0 0 0 2 0l7 -4.008c.619 -.355 1 -1.01 1 -1.718z" /><path d="M12 22v-10" /><path d="M12 12l8.73 -5.04" /><path d="M3.27 6.96l8.73 5.04" />
                            </svg>
                        </div>
                        <div class="card-content">
                            <h2>Available Equipment</h2>
                            <span><?= $total_available_equipments; ?></span>
                        </div>
                    </div>
<!--                     
                    <div class="card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cube"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 16.008v-8.018a1.98 1.98 0 0 0 -1 -1.717l-7 -4.008a2.016 2.016 0 0 0 -2 0l-7 4.008c-.619 .355 -1 1.01 -1 1.718v8.018c0 .709 .381 1.363 1 1.717l7 4.008a2.016 2.016 0 0 0 2 0l7 -4.008c.619 -.355 1 -1.01 1 -1.718z" /><path d="M12 22v-10" /><path d="M12 12l8.73 -5.04" /><path d="M3.27 6.96l8.73 5.04" />
                            </svg>
                        </div>
                        <div class="card-content">
                            <h2>Pending Requests</h2>
                            <span><?= $total_my_pending_requests; ?></span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cube"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 16.008v-8.018a1.98 1.98 0 0 0 -1 -1.717l-7 -4.008a2.016 2.016 0 0 0 -2 0l-7 4.008c-.619 .355 -1 1.01 -1 1.718v8.018c0 .709 .381 1.363 1 1.717l7 4.008a2.016 2.016 0 0 0 2 0l7 -4.008c.619 -.355 1 -1.01 1 -1.718z" /><path d="M12 22v-10" /><path d="M12 12l8.73 -5.04" /><path d="M3.27 6.96l8.73 5.04" />
                            </svg>
                        </div>  
                        <div class="card-content">
                            <h2>Returned Items</h2>
                            <span><?= $total_my_returned_items; ?></span>
                        </div>
                    </div> -->
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 overview-table">
                    <div class="flex justify-between items-center overview-table-header mb-4">
                        <h2>My Borrowed Items</h2>
                        <a href="<?= $base_url ?>/students/borrowed" class="button-medium gray">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="table-head">Equipment</th>
                                    <th class="table-head">Quantity</th>
                                    <th class="table-head">Date Borrowed</th>
                                    <th class="table-head">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($my_borrowed_items)): ?>
                                    <?php foreach ($my_borrowed_items as $item): ?>
                                        <tr>
                                            <td class="table-data"><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td class="table-data"><?= $item['quantity'] ?></td>
                                            <td class="table-data"><?= date('M d, Y', strtotime($item['date_borrowed'])) ?></td>
                                            <td class="table-data">
                                                <span class="
                                                    <?php 
                                                        if ($item['status'] == 'Approved') echo 'flex justify-center bg-[#D0E9DB] w-full text-[#16904D] py-2 px-5 rounded-[5px]'; 
                                                        elseif ($item['status'] == 'Pending') echo 'flex justify-center bg-[#FDEFDA] w-full text-[#B6771E] py-2 px-5 rounded-[5px]';
                                                        else echo 'flex justify-center bg-[#F0D2D2] text-[#B61E1E] py-2 px-5 w-full rounded-[5px]';
                                                    ?>
                                                ">
                                                    <?= $item['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center pt-7 text-gray-500">You haven't borrowed any items yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Available Equipment List -->
            <div class="w-6/12 max-[1320px]:w-full">
                <div class="bg-white shadow-md rounded-lg p-6 overview-table">
                    <div class="flex justify-between items-center overview-table-header mb-4">
                        <h2>Available Equipment</h2>
                        <a href="<?= $base_url ?>/students/inventory" class="button-medium gray">View All</a>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="table-head">Equipment</th>
                                    <th class="table-head">Category</th>
                                    <th class="table-head">Available</th>
                                </tr>
                            </thead>
                           <tbody>
                                <?php if (!empty($available_equipment)): ?>
                                    <?php foreach ($available_equipment as $equipment): ?>
                                        <tr>
                                            <td class="table-data"><?= htmlspecialchars($equipment['item_name']) ?></td>
                                            <td class="table-data"><?= htmlspecialchars($equipment['category'] ?: 'General') ?></td>
                                            <td class="table-data">
                                                <span class="flex justify-center bg-[#D0E9DB] w-full text-[#16904D] py-2 px-5 rounded-[5px]">
                                                    <?= $equipment['total_available'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">No equipment available at the moment.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</section>

<?php include 'students-layout-footer.php'; ?>
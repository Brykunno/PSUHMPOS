<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9f9;
    }

    #cash {
        direction: rtl;
        text-align: left;
        padding-right: 30px;
    }

    .keypad-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        width: 100%;
        padding: 10px;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }

    .keypad-container button {
        font-size: 2vw;
        padding: 8vw;
        border: 2px solid #ccc;
        border-radius: 8px;
        background-color: #fff;
        color: #333;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .keypad-container button:hover {
        background-color: rgb(44, 46, 167);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: #fff;
    }

    .keypad-container button:active {
        background-color: #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .keypad-container button.btn-outline-dark {
        background-color: #f7f7f7;
        color: #333;
        border-color: #bbb;
    }

    .keypad-container button.btn-outline-dark:hover {
        background-color: #ddd;
    }

    .keypad-container button.btn-danger {
        background-color: #f44336;
        color: white;
        border: none;
    }

    .keypad-container button.btn-danger:hover {
        background-color: #e53935;
    }

    .keypad-container button.btn-warning {
        background-color: #ff9800;
        color: white;
        border: none;
    }

    .keypad-container button.btn-warning:hover {
        background-color: #fb8c00;
    }

    input[type="text"] {
        font-size: 2vw;
        padding: 10px;
        width: 100%;
        text-align: right;
        background-color: #f7f7f7;
        border: 2px solid #ccc;
        border-radius: 8px;
        margin-bottom: 15px;
        color: #333;
        font-weight: bold;
        box-sizing: border-box;
        transition: border 0.3s ease;
    }

    input[type="text"]:focus {
        border-color: #007bff;
        outline: none;
    }

    #pos-field {
        height: 54em;
        display: flex;
        gap: 15px;
    }

    #menu-list {
        width: 65%;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(4px);
    }

    #order-list {
        width: 36%;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.7);
        border-left: 2px solid #ddd;
        padding: 15px;
        border-radius: 10px;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    #cat-list {
        height: 4em !important;
        overflow-x: auto;
        display: flex;
        gap: 8px;
        padding: 10px;
        white-space: nowrap;
        background: #ffffff;
        border-bottom: 2px solid #ddd;
    }

    #cat-list button,
    #cat-list .btn {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
    }

    #cat-list button:hover,
    #cat-list .btn:hover {
        color: black !important;
        background-color: #eaeaea;
        transform: scale(1.05);
        border-color: #ccc;
    }

    #item-list {
        height: 40em !important;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        padding: 15px;
        overflow-y: auto;
        justify-content: flex-start;
    }

    #item-list.empty-data {
        width: 100%;
        align-items: center;
        justify-content: center;
        display: flex;
    }

    #item-list.empty-data:after {
        content: 'Selected category has no menu items yet.';
        color: #b7b4b4;
        font-size: 1.7em;
        font-style: italic;
    }

    .menu-item {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        width: 31%;
        overflow: hidden;
    }

    .menu-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .menu-item button {
        width: 100%;
        height: 100%;
        padding: 15px;
        font-size: 1em;
        font-weight: bold;
        border-radius: 8px;
        background-color: #fff;
        color: #333;
        border: 2px solid #ccc;
        transition: background-color 0.3s ease, transform 0.3s ease, color 0.3s ease;
    }

    .menu-item button:hover {
        background-color: #f8f9fa;
        transform: scale(1.05);
        color: black !important;
    }

    #order-items-holder {
        height: 38em !important;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        padding: 10px;
        box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.05);
    }

    #order-items-header {
        position: sticky;
        top: 0;
        background: rgba(255, 255, 255, 0.9);
        padding: 10px;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
        z-index: 1;
        border-radius: 8px 8px 0 0;
        color: #000;
    }

    #order-items-body {
        margin-top: 5px;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    .btn {
        transition: background-color 0.3s, color 0.3s;
        font-weight: 600;
    }

    .btn:hover {
        
        color: #000 !important;
    }

    .btn-warning {
        background-color: #ffcc00;
        color: #000;
    }

    .btn-default {
        background-color: #f8f9fa;
        color: #000;
    }

    .btn-default:hover {
        background-color: #e2e6ea;
        color: #000000;
    }

    .d-flex h3 {
        font-size: 1.2em;
        font-weight: bold;
    }

    @media (max-width: 790px) {
        #pos-field {
            flex-direction: column;
        }

        #menu-list,
        #order-list {
            width: 100%;
        }

        .menu-item {
            width: 100% !important;
        }
    }

    .kitchen-header-gradient {
        background: linear-gradient(45deg, #219ebc, rgb(75, 40, 202));
        color: #fff;
    }

    .menu-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 10px;
    }

    .modal-content {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-title {
        font-size: 1.5rem;
    }

    .list-group-item {
        border: none;
        padding: 10px 15px;
    }

    .list-group-item:nth-child(odd) {
        background-color: #f8f9fa;
    }

    .list-group-item:nth-child(even) {
        background-color: #e9ecef;
    }

    .modal-body {
        font-size: 1rem;
    }

    .modal-footer {
        border-top: none;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-primary:hover {
        background-color: #0056b3;

    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    /* Order Type Section Styles */


.order-type-container {

    top: 20px;
    width: 280px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 15px;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    backdrop-filter: blur(10px);
}

.order-type-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 16px;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 8px;
}

.order-type-title i {
    color: #007bff;
    margin-right: 8px;
}

.order-type-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 15px;
}

.order-type-card {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.order-type-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.15);
}

.order-type-card.active {
    border-color: #007bff;
    background: linear-gradient(145deg, #e3f2fd, #f0f8ff);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

.card-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(145deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.card-icon i {
    color: white;
    font-size: 18px;
}

.card-content {
    flex: 1;
}

.card-content h6 {
    margin: 0;
    font-weight: 600;
    color: #495057;
    font-size: 15px;
}

.card-content small {
    color: #6c757d;
    font-size: 12px;
}

.card-radio {
    position: relative;
}

.card-radio input[type="radio"] {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #dee2e6;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.card-radio input[type="radio"]:checked {
    border-color: #007bff;
    background: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
}

.card-radio input[type="radio"]:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}

.table-selection-container {
    margin-top: 15px;
    padding: 15px;
    background: linear-gradient(145deg, #f8f9fa, #ffffff);
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.table-selection-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-weight: 600;
    color: #495057;
}

.table-selection-header i {
    color: #28a745;
    margin-right: 8px;
}

.table-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    max-height: 200px;
    overflow-y: auto;
}

.table-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.table-option:hover {
    border-color: #28a745;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
}

.table-option.selected {
    border-color: #28a745;
    background: linear-gradient(145deg, #d4edda, #f0fff4);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.table-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(145deg, #28a745, #1e7e34);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.table-icon i {
    color: white;
    font-size: 14px;
}

.table-info {
    text-align: center;
}

.table-number {
    font-weight: 600;
    color: #495057;
    font-size: 13px;
}

.table-info small {
    color: #6c757d;
    font-size: 11px;
}

.table-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.no-tables-available {
    grid-column: 1 / -1;
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.no-tables-available i {
    font-size: 24px;
    margin-bottom: 8px;
}

.order-summary {
    margin-top: 15px;
    padding: 12px;
    background: linear-gradient(145deg, #d4edda, #f0fff4);
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.summary-content {
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #155724;
}

.summary-content i {
    margin-right: 8px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .order-type-container {
        padding: 15px;
    }
    
    .table-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }
}

/* Shake animation for validation feedback */
.shake {
    animation: shake 0.6s;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-5px);
    }
}

/* Modal Styles for Order Type Selection */
.order-type-card-modal {
    cursor: pointer;
    transition: all 0.3s ease;
}

.order-type-card-modal .card {
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.order-type-card-modal:hover .card {
    border-color: #007bff;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
}

.order-type-card-modal.active .card {
    border-color: #007bff;
    background: linear-gradient(145deg, #e3f2fd, #f0f8ff);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
}

.card-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(145deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.card-icon-large i {
    color: white;
    font-size: 32px;
}

.card-radio-large {
    position: relative;
    margin-top: 15px;
}

.card-radio-large input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.radio-label {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 3px solid #dee2e6;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.card-radio-large input[type="radio"]:checked + .radio-label {
    border-color: #007bff;
    background: #007bff;
}

.card-radio-large input[type="radio"]:checked + .radio-label::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: white;
}

.table-grid-modal {
    max-height: 300px;
    overflow-y: auto;
}

.table-option-modal {
    cursor: pointer;
    transition: all 0.3s ease;
}

.table-option-modal .card {
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.table-option-modal:hover .card {
    border-color: #28a745;
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.15);
}

.table-option-modal.selected .card {
    border-color: #28a745;
    background: linear-gradient(145deg, #d4edda, #f0fff4);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.2);
}

.table-icon-modal {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(145deg, #28a745, #1e7e34);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.table-icon-modal i {
    color: white;
    font-size: 16px;
}

.table-selection-section {
    margin-top: 20px;
}

#selectOrderTypeBtn {
    border-radius: 8px;
    font-weight: 600;
    padding: 12px;
    transition: all 0.3s ease;
}

#selectOrderTypeBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.order-type-selection {
    margin-bottom: 20px;
}

/* Status badges for the button */
.btn-success {
    background: linear-gradient(145deg, #28a745, #1e7e34);
}

.btn-warning {
    background: linear-gradient(145deg, #ffc107, #e0a800);
}
</style>

<?php
$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;
?>

<div class="content kitchen-header-gradient py-3 px-4 rounded shadow-sm mb-3">
    <h3 class="font-weight-bolder text-light mb-0"></h3>
</div>
<div class="row mt-n4 justify-content-center">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0">
            <div class="card-body">
                <form action="" id="sales-form">
                    <input type="hidden" name="total_amount" value="0">
                       
    <input type="hidden" name="table_id" value="<?= $table_id ?>">


                    <div id="pos-field">
                        <div id="menu-list">
                            <fieldset>
                                <legend>Categories</legend>
                           <div id="cat-list" class="py-1">
    <!-- Make Best Sellers active by default -->
    <button class="btn btn-xs rounded-pill px-2 cat_btn mx-3 col-lg-3 col-md-4 col-sm-6 col-xs-10 bg-gradient-warning text-light" type="button" data-id='0'>Best Sellers</button>
    <?php 
    $categories = $conn->query("SELECT * FROM category_list where delete_flag = 0 and status = 1 order by name asc");
    while($row = $categories->fetch_assoc()):
    ?>
    <!-- Remove active styling from other categories -->
    <button class="btn btn-xs rounded-pill px-2 cat_btn mx-3 col-lg-3 col-md-4 col-sm-6 col-xs-10 bg-gradient-light border" type="button" data-id='<?= $row['id'] ?>'><?= $row['name'] ?></button>
    <?php endwhile; ?>
</div>
                            </fieldset>
                            <fieldset>
                                <legend>Menu</legend>
                                <div id="item-list" class="py-1 overflow-auto">
                                              <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 gy-2 gx-2">
                                    <?php 

                                    $cat_list = $conn->query("SELECT name from category_list");
                                    while($cat_row = $cat_list->fetch_assoc()):
                                     $cat_name = $cat_row["name"];

                                    ?>

                                    <br/>

                                    <?php
                                       
                           
                                    $bestSellers = $conn->query("SELECT *,
                                                        COUNT(order_items.menu_id) AS total_orders,category_list.name AS cat_name, menu_list.name AS menu_name
                                                    FROM menu_list
                                                    INNER JOIN order_items 
                                                        ON menu_list.id = order_items.menu_id
                                                    INNER JOIN category_list ON category_list.id = menu_list.category_id
                                                    WHERE menu_list.delete_flag = 0 
                                                    AND menu_list.status = 1
                                                    AND category_list.name = '$cat_name'
                                                    GROUP BY menu_list.id, menu_list.name
                                                    ORDER BY total_orders DESC LIMIT 3;");
                                   
                                    while($row = $bestSellers->fetch_assoc()):
                                    ?>
                                    <div class="col <?= isset($cid) && $cid == $row['category_id'] ? "" : "d-none" ?> menu-item" data-cat-id='0'>
                                        <button class="btn btn-default btn-block btn-xs rounded px-2 bg-gradient-light border item-btn text-left" type="button" data-id='<?= $row['id'] ?>' data-price='<?= $row['price'] ?>'>
                                            <div class="d-flex flex-column align-items-center">
                                                <!-- Image should take up most of the space -->
                                                <img src="<?= isset($row['image_path']) && !empty($row['image_path']) ? base_url . 'uploads/image/' . htmlspecialchars($row['image_path']) : 'path_to_placeholder_image.jpg' ?>" 
                                                     alt="<?= htmlspecialchars($row['name']) ?>" 
                                                     style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px;">
                                                <!-- Text and price below the image -->
                                                <div class="text-center mt-2">
                                                    <div class="font-weight-bold"><?= htmlspecialchars($row['menu_name']) ?></div>

                                                    <small class="text-muted"><?= $row['code'] ?></small>
                                                    <br/>
                                                        <div class="menu-stats-container">
        <div class="stat-item orders">
            <i class="fas fa-chart-line"></i>
            <span class="stat-number"><?= $row['total_orders'] ?></span>
            <span class="stat-label"><?= $row['total_orders']<2?"order":"orders" ?></span>
        </div>
        
        
        <div class="stat-item category">
            <i class="fas fa-utensils"></i>
            <span class="stat-text"><?= $cat_name ?></span>
        </div>
    </div>


                                                    <div class="font-weight-bold mt-1">₱<?= number_format($row['price'], 2) ?></div> <!-- Peso sign added -->
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                    <?php endwhile;
                                    endwhile; ?>
                                    </div>


                                    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 gy-2 gx-2">
                                    <?php 
                                    $menus = $conn->query("SELECT * FROM menu_list where  delete_flag = 0 and status = 1 order by name asc");
                                    while($row = $menus->fetch_assoc()):
                                    ?>
                                    <div class="col <?= isset($cid) && $cid == $row['category_id'] ? "" : "d-none" ?> menu-item" data-cat-id='<?= $row['category_id'] ?>'>
                                        <button class="btn btn-default btn-block btn-xs rounded px-2 bg-gradient-light border item-btn text-left" type="button" data-id='<?= $row['id'] ?>' data-price='<?= $row['price'] ?>'>
                                            <div class="d-flex flex-column align-items-center">
                                                <!-- Image should take up most of the space -->
                                                <img src="<?= isset($row['image_path']) && !empty($row['image_path']) ? base_url . 'uploads/image/' . htmlspecialchars($row['image_path']) : 'path_to_placeholder_image.jpg' ?>" 
                                                     alt="<?= htmlspecialchars($row['name']) ?>" 
                                                     style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px;">
                                                <!-- Text and price below the image -->
                                                <div class="text-center mt-2">
                                                    <div class="font-weight-bold"><?= htmlspecialchars($row['name']) ?></div>
                                                    <small class="text-muted"><?= $row['code'] ?></small>
                                                    <div class="font-weight-bold mt-1">₱<?= number_format($row['price'], 2) ?></div> <!-- Peso sign added -->
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                    <?php endwhile; ?>
                                    </div>
                                    
                                </div>
                                
                            </fieldset>
                            <div class="text-center py-2">
                                <button type="submit" id="place_order" class="btn btn-warning bg-gradient-warning rounded-pill px-4">Place Order</button>
                            </div>
                        </div>
                        <div id="order-list" class="bg-gradient-dark p-1">
                            <h4><b>Orders</b></h4>
                            <div id="order-items-holder" class="bg-gradient-light mb-3">
                                <div id="order-items-header">
                                    <div class="d-flex w-100 bg-gradient-warning">
                                        <div class="col-3 text-center font-weight-bolder m-0 border">Quantity</div>
                                        <div class="col-6 text-center font-weight-bolder m-0 border">Menu</div>
                                        <div class="col-3 text-center font-weight-bolder m-0 border">Total</div>
                                    </div>
                                </div>
                                <div id="order-items-body"></div>
                            </div>
                          <!-- VAT -->
<div class="d-none justify-content-between align-items-center w-100 my-1 ">
    <small class="mb-0 font-weight-bold text-white">VAT (12%)</small>
    <small class="mb-0 text-white text-right" id="vat_total">₱0.00</small> <!-- Peso sign added -->
</div>

<!-- Discount -->
<div class="justify-content-between align-items-center w-100 my-1 d-none">
    <small class="mb-0 font-weight-bold text-white">Discount</small>
    <select id="discount_type" name="discount_type" class="form-control form-control-sm rounded-0 bg-light font-weight-bold" style="width: 120px; font-size: 12px;">
        <option value="N/A" data-percent="0" selected>N/A</option>
        <?php
        $discounts = $conn->query("SELECT * FROM discount_list");
        while($discount = $discounts->fetch_assoc()):
        ?>
            <option value="<?= $discount['name'] ?>" data-percent="<?= $discount['percentage'] ?>">
                <?= $discount['name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<!-- Grand Total -->
<div class="d-flex justify-content-between align-items-center w-100 mt-2 pt-2 border-top">
    <span class="font-weight-bold text-white" style="font-size: 16px;">Grand Total</span>
    <span class="text-white text-right" style="font-size: 16px;" id="grand_total">₱0.00</span> <!-- Peso sign added -->
</div>

<!-- Cash Tendered and Change -->
<div class="mt-4 d-none">
    <label for="cash">Cash Tendered</label>
    <input type="text" id="cash" name="tendered_amount" class="form-control text-end fw-bold" autocomplete="off" inputmode="decimal">
    
    <label for="change">Change</label>
    <div id="change" class="form-control text-end fw-bold bg-light">₱0.00</div> 
    </div>
    <!-- Peso sign added -->

<!-- Button to open modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    Select Order Type
</button>





<div class="keypad-container mt-3 d-grid gap-2 d-none" style="grid-template-columns: repeat(3, 1fr); max-width: 250px;">
    <button type="button" onclick="appendCash('1', event)" class="btn btn-outline-dark px-4 py-2">1</button>
    <button type="button" onclick="appendCash('2', event)" class="btn btn-outline-dark px-4 py-2">2</button>
    <button type="button" onclick="appendCash('3', event)" class="btn btn-outline-dark px-4 py-2">3</button>
    <button type="button" onclick="appendCash('4', event)" class="btn btn-outline-dark px-4 py-2">4</button>
    <button type="button" onclick="appendCash('5', event)" class="btn btn-outline-dark px-4 py-2">5</button>
    <button type="button" onclick="appendCash('6', event)" class="btn btn-outline-dark px-4 py-2">6</button>
    <button type="button" onclick="appendCash('7', event)" class="btn btn-outline-dark px-4 py-2">7</button>
    <button type="button" onclick="appendCash('8', event)" class="btn btn-outline-dark px-4 py-2">8</button>
    <button type="button" onclick="appendCash('9', event)" class="btn btn-outline-dark px-4 py-2">9</button>
    <button type="button" onclick="appendCash('0', event)" class="btn btn-outline-dark px-4 py-2">0</button>
    <button type="button" onclick="clearCash(event)" class="btn btn-danger px-4 py-2">Clear</button>
    <button type="button" onclick="backspaceCash(event)" class="btn btn-outline-dark px-4 py-2">←</button>
</div>
                        </div>
                    </div>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Order Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body mx-auto ">
             

<!-- Order Type Section - Improved UI -->
<div class="order-type-section mx-auto" >
    <div >
        <div class="order-type-container">
            <h6 class="order-type-title">
                <i class="fas fa-clipboard-list"></i>
                Order Type
            </h6>
            
            <!-- Order Type Cards -->
            <div class="order-type-cards">
                <div class="order-type-card" data-type="Dine-In">
                    <div class="card-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="card-content">
                        <h6>Dine-In</h6>
                        <small>Customer will eat at restaurant</small>
                    </div>
                    <div class="card-radio">
                        <input type="radio" name="order_type" value="Dine-In" id="dine_in" required>
                        <label for="dine_in"></label>
                    </div>
                </div>
                
                <div class="order-type-card" data-type="Takeaway">
                    <div class="card-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="card-content">
                        <h6>Takeaway</h6>
                        <small>Customer will take order to go</small>
                    </div>
                    <div class="card-radio">
                        <input type="radio" name="order_type" value="Takeaway" id="takeaway" required>
                        <label for="takeaway"></label>
                    </div>
                </div>
            </div>
            
            <!-- Table Selection (only shown for Dine-In) -->
            <div id="table-selection-improved" class="table-selection-container" style="display: none;">
                <div class="table-selection-header">
                    <i class="fas fa-chair"></i>
                    <span>Select Table</span>
                </div>
                
                <div class="table-grid">
                    <?php 
                    // Get available tables
                    $available_tables = $conn->query("SELECT t.* FROM table_list t 
                                                     LEFT JOIN order_list o ON t.id = o.table_id AND o.status IN (0,1) 
                                                     WHERE t.delete_flag = 0 AND t.status = 1 AND o.id IS NULL 
                                                     ORDER BY CAST(SUBSTRING(t.table_number, 6) AS UNSIGNED)");
                    
                    if ($available_tables->num_rows > 0):
                        while($table = $available_tables->fetch_assoc()): 
                    ?>
                    <div class="table-option" data-table-id="<?= $table['id'] ?>" data-capacity="<?= $table['capacity'] ?>">
                        <div class="table-icon">
                            <i class="fas fa-chair"></i>
                        </div>
                        <div class="table-info">
                            <span class="table-number"><?= $table['table_number'] ?></span>
                            <small><?= $table['capacity'] ?> seats</small>
                        </div>
                        <input type="radio" name="selected_table" value="<?= $table['id'] ?>" id="table_<?= $table['id'] ?>">
                    </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <div class="no-tables-available">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <p>No tables available at the moment</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Selected Order Summary -->
            <div id="order-type-summary" class="order-summary" style="display: none;">
                <div class="summary-content">
                    <i class="fas fa-check-circle text-success"></i>
                    <span id="summary-text"></span>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->

            
            <!-- Modal Footer -->
        
        </div>
    </div>
</div>

<div class="modal fade" id="orderReviewModal" tabindex="-1" role="dialog" aria-labelledby="orderReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderReviewModalLabel">Confirm Your Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Your Order:</h5>
                <div id="order-review-list"></div>
                <div class="d-flex justify-content-between mt-3">
                    <strong>Total:</strong>
                    <span id="review-total">₱0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <strong>VAT (12%):</strong>
                    <span id="review-vat">₱0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <strong>Discount:</strong>
                    <span id="review-discount">₱0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <strong>Grand Total:</strong>
                    <span id="review-grand-total">₱0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-order" class="btn btn-warning bg-gradient-warning">Confirm Order</button>
            </div>
        </div>
    </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="orderConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="orderConfirmationModalLabel">Confirm Your Order</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6 class="font-weight-bold">Order Summary:</h6>
        <ul id="orderSummaryList" class="list-group mb-3"></ul>
        
        <div class="mb-2">
          <strong>Order Type:</strong> 
          <span id="orderTypeSummary" class="badge badge-info"></span>
        </div>
        <div class="mb-2">
          <strong>Discount:</strong> 
          <span id="discountSummary" class="badge badge-success"></span>
        </div>
        <div class="mb-2">
          <strong>Total Amount:</strong> 
          <span id="totalAmountSummary" class="font-weight-bold"></span>
        </div>
        <div class="mb-2">
          <strong>Tendered Amount:</strong> 
          <span id="tenderedAmountSummary" class="font-weight-bold"></span>
        </div>
        <div class="mb-2">
          <strong>Change:</strong> 
          <span id="changeSummary" class="font-weight-bold text-danger"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmOrderButton">Confirm Order</button>
      </div>
    </div>
  </div>
</div>
<noscript id="item-clone">
<div class="d-flex w-100 bg-gradient-light product-item">
    <div class="col-3 text-center font-weight-bolder m-0 border align-middle">
        <input type="hidden" name="menu_id[]" value="">
        <input type="hidden" name="price[]" value="">
        <div class="input-group input-group-sm">
            <button class="btn btn-warning btn-xs btn-flat minus-qty" type="button"><i class="fa fa-minus"></i></button>
            <input type="number" min = '1' value='1' name="quantity[]" class="form-control form-control-xs rounded-0 text-center" required readonly>
            <button class="btn btn-warning btn-xs btn-flat plus-qty" type="button"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="col-6 font-weight-bolder m-0 border align-middle">
        <div style="line-height:1em" class="text-sm">
        <div class="w-100 d-flex align-items-center"><a href="javascript:void(0)" class="text-danger text-decoration-none rem-item mr-1"><i class="fa fa-times"></i></a> <p class="m-0 truncate-1 menu_name">Menu name</p></div>
        <div><small class="text-muted menu_price">x 0.00</small></div>
        </div>
    </div>
    <div class="col-3 font-weight-bolder m-0 border align-middle text-right menu_total">0.00</div>
</div>

</noscript>
<script>
document.getElementById('cash').addEventListener('input', function () {
    // Only allow digits and optional one dot
    this.value = this.value.replace(/[^0-9.]/g, '');

    // Prevent more than one dot
    if ((this.value.match(/\./g) || []).length > 1) {
        this.value = this.value.slice(0, -1);
    }
});

function backspaceCash(event) {
  event.preventDefault();
  const input = document.getElementById('cash');
  input.value = input.value.slice(0, -1);
  calculateChange();
}

function appendCash(val, event) {
  event.preventDefault();
  const input = document.getElementById('cash');
  input.value += val;
  calculateChange();
}

function clearCash(event) {
  event.preventDefault();
  const input = document.getElementById('cash');
  input.value = '';
  calculateChange();
}

function calculateChange() {
  const total = parseFloat(document.querySelector('input[name="total_amount"]').value) || 0;
  const tendered = parseFloat(document.getElementById('cash').value);

  if (isNaN(tendered) || tendered <= 0) {
    document.getElementById('change').textContent = '₱0.00';
    return;
  }

  const change = tendered - total;
  document.getElementById('change').textContent = '₱' + (change >= 0 ? change : 0).toLocaleString('en-US', {
    style: 'decimal',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

$(function() {
  $('body').addClass('sidebar-collapse');

    setTimeout(function() {
    $('.cat_btn[data-id="0"]').trigger('click');
  }, 100);

  toggleEmptyData();


  $('#discount_type').on('change', calc_total);

  $('.cat_btn').click(function() {
    $('.cat_btn.bg-gradient-warning').removeClass('bg-gradient-warning text-light').addClass('bg-gradient-light border');
    $(this).removeClass('bg-gradient-light border').addClass('bg-gradient-warning text-light');

    const id = $(this).attr('data-id');
    $('.menu-item').addClass('d-none');
    $(`.menu-item[data-cat-id="${id}"]`).removeClass('d-none');

    toggleEmptyData();
  });

  function toggleEmptyData() {
    if ($('#item-list>.row>.col:visible').length > 0) {
      $('#item-list').removeClass('empty-data');
    } else {
      $('#item-list').addClass('empty-data');
    }
  }

  function calc_total() {
    let subtotal = 0;
    $('#order-items-body .product-item').each(function() {
      const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
      const qty = parseInt($(this).find('input[name="quantity[]"]').val()) || 0;
      const itemTotal = price * qty;

      $(this).find('.menu_total').text(itemTotal.toLocaleString('en-US', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }));

      subtotal += itemTotal;
    });

    let grandTotal = subtotal;
    const discountAmount = grandTotal * (0 / 100);
    grandTotal -= discountAmount;

    $('[name="total_amount"]').val(grandTotal);
    $('#grand_total').text('₱' + grandTotal.toFixed(2));
    $('#discount_value').text('₱' + discountAmount.toFixed(2));

    calculateChange();
  }

  $('.item-btn').click(function() {
    const id = $(this).attr('data-id');
    const price = $(this).attr('data-price');
    const name = $(this).text().trim();
    let item = $($('noscript#item-clone').html()).clone();

    if ($('#order-items-body .product-item[data-id="' + id + '"]').length > 0) {
      item = $('#order-items-body .product-item[data-id="' + id + '"]');
      let qty = parseInt(item.find('input[name="quantity[]"]').val()) || 0;
      qty++;
      item.find('input[name="quantity[]"]').val(qty);
      calc_total();
      return;
    }

    item.attr('data-id', id);
    item.find('input[name="menu_id[]"]').val(id);
    item.find('input[name="price[]"]').val(price);
    item.find('.menu_name').text(name);
    item.find('.menu_price').text("x " + parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2 }));
    item.find('.menu_total').text(parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2 }));

    $('#order-items-body').append(item);
    calc_total();

    item.find('.minus-qty').click(function() {
      let qty = parseInt(item.find('input[name="quantity[]"]').val()) || 0;
      qty = qty <= 1 ? 1 : qty - 1;
      item.find('input[name="quantity[]"]').val(qty);
      calc_total();
    });

    item.find('.plus-qty').click(function() {
      let qty = parseInt(item.find('input[name="quantity[]"]').val()) || 0;
      item.find('input[name="quantity[]"]').val(qty + 1);
      calc_total();
    });

    $(document).on('click', '.rem-item', function() {
      $(this).closest('.product-item').remove();
      calc_total();
    });
  });

  $('input[name="tendered_amount"], input[name="total_amount"]').on('input change', calculateChange);

  // Handle order type selection
  $('.order-type-card').on('click', function() {
    const orderType = $(this).data('type');
    
    // Remove active class from all cards
    $('.order-type-card').removeClass('active');
    
    // Add active class to clicked card
    $(this).addClass('active');
    
    // Check the radio button
    $(this).find('input[type="radio"]').prop('checked', true);
    
    // Handle table selection visibility
    if (orderType === 'Dine-In') {
      $('#table-selection-improved').slideDown(300);
      $('input[name="selected_table"]').prop('required', true);
      $('#order-type-summary').hide();
    } else {
      $('#table-selection-improved').slideUp(300);
      $('input[name="selected_table"]').prop('required', false);
      $('.table-option').removeClass('selected');
      $('input[name="selected_table"]').prop('checked', false);
      $('input[name="table_id"]').val('');
      
      // Show summary for takeaway
      showOrderSummary(orderType);
    }
  });

  // Handle table selection
  $('.table-option').on('click', function() {
    const tableId = $(this).data('table-id');
    const capacity = $(this).data('capacity');
    const tableNumber = $(this).find('.table-number').text();
    
    // Remove selected class from all table options
    $('.table-option').removeClass('selected');
    
    // Add selected class to clicked option
    $(this).addClass('selected');
    
    // Check the radio button
    $(this).find('input[type="radio"]').prop('checked', true);
    
    // Update hidden table_id field
    $('input[name="table_id"]').val(tableId);
    
    // Show summary
    showOrderSummary('Dine-In', `${tableNumber} (${capacity} seats)`);
  });

  // Pre-select table if coming from table management
  const urlParams = new URLSearchParams(window.location.search);
  const preSelectedTableId = urlParams.get('table_id');
  
  if (preSelectedTableId) {
    // Select Dine-In automatically
    $('.order-type-card[data-type="Dine-In"]').click();
    
    // Select the table
    setTimeout(() => {
      $(`.table-option[data-table-id="${preSelectedTableId}"]`).click();
    }, 100);
  }

  function showOrderSummary(orderType, tableInfo = '') {
    let summaryText = '';
    
    if (orderType === 'Dine-In' && tableInfo) {
      summaryText = `Dine-In • Table: ${tableInfo}`;
    } else if (orderType === 'Takeaway') {
      summaryText = 'Takeaway Order';
    }
    
    if (summaryText) {
      $('#summary-text').text(summaryText);
      $('#order-type-summary').slideDown(300);
    }
  }

  const plcaOrderBtn = document.querySelector("#place_order");
  plcaOrderBtn.addEventListener("click",()=> {


    // Check if there are order items
    if ($('#order-items-body .product-item').length <= 0) {
      alert_toast("Please Add at least 1 Item First.", "warning");
      return;
    }

    // Check if order type is selected using the correct input name
    const orderType = $('input[name="order_type"]:checked').val();
    if (!orderType) {
      alert_toast("Please select an order type (Dine-In or Takeaway).", "error");
      $('.order-type-card').addClass('shake');
      setTimeout(() => $('.order-type-card').removeClass('shake'), 600);
      return;
    }

    // Check if table is selected for Dine-In orders
    if (orderType === 'Dine-In') {
      const selectedTable = $('input[name="selected_table"]:checked').val();
      if (!selectedTable) {
        alert_toast("Please select a table for Dine-In orders.", "error");
        $('.table-option').addClass('shake');
        setTimeout(() => $('.table-option').removeClass('shake'), 600);
        return;
      }
    }

    // Prepare order summary
    let orderSummary = '';
    $('#order-items-body .product-item').each(function() {
      const name = $(this).find('.menu_name').text();
      const qty = $(this).find('input[name="quantity[]"]').val();
      const price = parseFloat($(this).find('input[name="price[]"]').val());
      const total = (qty * price).toFixed(2);
      orderSummary += `<li class="list-group-item d-flex justify-content-between">
                          <span>${name} x ${qty}</span>
                          <strong>₱${total}</strong>
                       </li>`;
    });

    // Add order type info to summary
    let orderTypeInfo = `<span class="badge badge-primary">${orderType}</span>`;
    if (orderType === 'Dine-In') {
      const selectedTableOption = $('.table-option.selected');
      if (selectedTableOption.length > 0) {
        const tableName = selectedTableOption.find('.table-number').text();
        const tableCapacity = selectedTableOption.data('capacity');
        orderTypeInfo += ` • Table: ${tableName} (${tableCapacity} seats)`;
      }
    }

    // Show enhanced modal
    $('#orderConfirmationModal .modal-body').html(`
      <div class="alert alert-info">
        <strong>Order Type:</strong> ${orderTypeInfo}
      </div>
      
      <h6 class="font-weight-bold">Order Items:</h6>
      <ul class="list-group mb-3">${orderSummary}</ul>
      
      <div class="d-flex justify-content-between mt-3">
        <strong>Total Amount:</strong>
        <span class="font-weight-bold text-primary">₱${parseFloat($('input[name="total_amount"]').val()).toFixed(2)}</span>
      </div>
    `);

    $('#orderConfirmationModal').modal('show');
  })



  // Fixed form submission with proper validation
  $('#sales-form').submit(function(e) {
    e.preventDefault();

    // Check if there are order items
    if ($('#order-items-body .product-item').length <= 0) {
      alert_toast("Please Add at least 1 Item First.", "warning");
      return;
    }

    // Check if order type is selected using the correct input name
    const orderType = $('input[name="order_type"]:checked').val();
    if (!orderType) {
      alert_toast("Please select an order type (Dine-In or Takeaway).", "error");
      $('.order-type-card').addClass('shake');
      setTimeout(() => $('.order-type-card').removeClass('shake'), 600);
      return;
    }

    // Check if table is selected for Dine-In orders
    if (orderType === 'Dine-In') {
      const selectedTable = $('input[name="selected_table"]:checked').val();
      if (!selectedTable) {
        alert_toast("Please select a table for Dine-In orders.", "error");
        $('.table-option').addClass('shake');
        setTimeout(() => $('.table-option').removeClass('shake'), 600);
        return;
      }
    }

    // Prepare order summary
    let orderSummary = '';
    $('#order-items-body .product-item').each(function() {
      const name = $(this).find('.menu_name').text();
      const qty = $(this).find('input[name="quantity[]"]').val();
      const price = parseFloat($(this).find('input[name="price[]"]').val());
      const total = (qty * price).toFixed(2);
      orderSummary += `<li class="list-group-item d-flex justify-content-between">
                          <span>${name} x ${qty}</span>
                          <strong>₱${total}</strong>
                       </li>`;
    });

    // Add order type info to summary
    let orderTypeInfo = `<span class="badge badge-primary">${orderType}</span>`;
    if (orderType === 'Dine-In') {
      const selectedTableOption = $('.table-option.selected');
      if (selectedTableOption.length > 0) {
        const tableName = selectedTableOption.find('.table-number').text();
        const tableCapacity = selectedTableOption.data('capacity');
        orderTypeInfo += ` • Table: ${tableName} (${tableCapacity} seats)`;
      }
    }

    // Show enhanced modal
    $('#orderConfirmationModal .modal-body').html(`
      <div class="alert alert-info">
        <strong>Order Type:</strong> ${orderTypeInfo}
      </div>
      
      <h6 class="font-weight-bold">Order Items:</h6>
      <ul class="list-group mb-3">${orderSummary}</ul>
      
      <div class="d-flex justify-content-between mt-3">
        <strong>Total Amount:</strong>
        <span class="font-weight-bold text-primary">₱${parseFloat($('input[name="total_amount"]').val()).toFixed(2)}</span>
      </div>
    `);

    $('#orderConfirmationModal').modal('show');
  });

  // Confirm order placement
  $('#confirmOrderButton').click(function() {
    const orderType = $('input[name="order_type"]:checked').val();
    let tableId = '';
    
    // Get table ID based on order type
    if (orderType === 'Dine-In') {
      tableId = $('input[name="selected_table"]:checked').val();
    }
    
    start_loader();
    
    // Prepare form data
    let formData = $('#sales-form').serialize();
    
    // Ensure table_id is included correctly
    if (tableId) {
      // Remove any existing table_id and add the correct one
      formData = formData.replace(/table_id=[^&]*&?/g, '');
      formData += '&table_id=' + tableId;
    }

    // Place the order using AJAX
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=place_order",
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function(resp) {
        if (resp.status === 'success') {
          alert_toast(resp.msg, 'success');
          
          // Redirect based on order type
          setTimeout(() => {
            if (orderType === 'Dine-In') {
              // Go back to table management
              window.location.href = _base_url_ + 'admin/?page=table_management';
            } else {
              // For takeaway, just reload the page
              location.reload();
            }
          }, 1000);
        } else {
          alert_toast(resp.msg || "Unknown error", 'error');
        }
        end_loader();
        $('#orderConfirmationModal').modal('hide');
      },
      error: function(err) {
        console.error('AJAX Error:', err);
        alert_toast("An error has occurred while placing the order.", "error");
        end_loader();
      }
    });
  });

  // Cancel order action
  $('#confirmCancel').click(function() {
    $('#orderConfirmationModal').modal('hide');
  });
});
</script>

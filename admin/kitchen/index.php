<style>
    /* Scrollbar Styling */
    #order-field, .order-list {
        scrollbar-width: thin;
        scrollbar-color: #5b9bd5 #f1f1f1;
    }

    #order-field::-webkit-scrollbar, .order-list::-webkit-scrollbar {
        width: 6px;
    }

    #order-field::-webkit-scrollbar-thumb, .order-list::-webkit-scrollbar-thumb {
        background-color: #5b9bd5;
        border-radius: 10px;
    }

    /* Order Field Container */
    #order-field {
        height: 54em;
        overflow-y: auto;
        padding: 1em;
        background: #f5faff;
        border-radius: 12px;
        box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.05);
    }

    #order-field:empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f7fc;
    }

    #order-field:empty:after {
        content: "No order has been queued yet.";
        color: #b0b0b0;
        font-size: 1.5em;
        font-style: italic;
    }

    /* Order List Table */
    .order-list {
        height: 18em;
        overflow-y: auto;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #d0e4f4;
        color: #000;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .order-list-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #5b9bd5;
        color: #ffffff;
        padding: 0.6em 1em;
        font-weight: bold;
        font-size: 1rem;
        border-bottom: 1px solid #4a87c4;
        letter-spacing: 0.5px;
    }

    .order-body > .d-flex {
        padding: 0.6em 1em;
        border-bottom: 1px solid #ebf3fa;
        align-items: center;
        color: #333;
        transition: background 0.3s ease;
    }

    .order-body > .d-flex:hover {
        background: #f1f8fd;
    }

    .card.rounded-0.shadow.card-outline.card-warning {
        border: 1px solid #c2dbf0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(91, 155, 213, 0.15);
        overflow: hidden;
    }

    .card-header {
        background-color: #e7f0fa;
        border-bottom: 1px solid #c7ddf2;
        font-size: 1.1em;
        color: #333;
        padding: 0.8em 1em;
    }

    .card-title {
        color:rgb(0, 0, 0);
        margin: 0;
    }

    .serve-button {
        background-color: #5b9bd5;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .serve-button:hover {
        background-color: #417fbd;
        transform: scale(1.05);
    }

    .order-served {
        background-color: #5b9bd5;
        color: #ffffff;
        font-weight: 600;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .order-served:hover {
        background-color: #417fbd;
        transform: scale(1.03);
    }

    .served-label {
        color: green;
        font-size: 0.9em;
        font-weight: bold;
        margin-left: 0.5em;
    }

    .item-served {
        text-decoration: line-through;
        color: #6c757d;
    }

    .order-checkbox {
        margin-right: 0.6em;
        transform: scale(1.2);
    }

    .order-item .d-flex > .col-9 {
        display: flex;
        align-items: center;
    }

    .discount-badge {
        display: inline-block;
        padding: 2px 10px;
        font-size: 0.8em;
        font-weight: 600;
        color: #fff;
        background-color: #5b9bd5;
        border-radius: 30px;
        margin-top: 5px;
        transition: background 0.3s ease;
    }

    .discount-badge:hover {
        background-color: #407ab1;
    }

    .category-label {
        margin-top: 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #fff;
        padding: 0.3rem 0.8rem;
        background-color: #5b9bd5;
        border-radius: 20px;
        display: inline-block;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .category-label:hover {
        background-color: #417fbd;
        color: #000;
    }

    body {
        background: #f1f9ff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #000;
    }

    .kitchen-header-gradient {
        background-color: #5b9bd5;
        color: #ffffff;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
</style>



<div class="content kitchen-header-gradient py-3 px-4 rounded shadow-sm mb-3">
    <h3 class="font-weight-bolder text-light mb-0">🍽️ Order List (Kitchen Side)</h3>
</div>

<div class="row mt-n4 justify-content-center">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 border-0 shadow-sm">
            <div class="card-body p-3">
                <div id="order-field" class="row row-cols-lg-3 row-cols-md-2 row-cols-sm-1 gx-2 py-1"></div>
            </div>
        </div>
    </div>
</div>

<noscript id="order-clone">
    <div class="col order-item mb-3">
        <div class="card rounded-0 shadow card-outline card-warning">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <b class="card-title m-0">Queue # <span class="queue-code">10001</span></b>
                    <div>
                         <span class="order-type badge bg-secondary text-light px-2 py-1 rounded-pill small">Order Type</span>
                     <span class="table-number badge bg-success text-light px-2 py-1 rounded-pill small">Order Type</span>
                    </div>
                   
                </div>
                <div class="discount-label mt-1"></div>
            </div>
            <div class="card-body pb-2 pt-1">
                <div class="order-list">
                    <div class="d-flex w-100 order-list-header">
                        <div class="col-9 m-0">Product</div>
                        <div class="col-3 m-0 text-center">QTY</div>
                    </div>
                    <div class="order-body"></div>
                </div>
            </div>
            <div class="card-footer py-2 text-center">
                <button class="btn btn-sm order-served px-4 rounded-pill w-75" type="button" data-id="">Serve</button>
            </div>
        </div>
    </div>
</noscript>

<script>
    function get_order() {
    let listed = []
    $('.order-item').each(function () {
     
        listed.push($(this).attr('data-id'))
    })

    $.ajax({
        url: _base_url_ + "classes/Master.php?f=get_order",
        method: 'POST',
        data: { listed: listed },
        dataType: 'json',
        error: err => {
            console.log(err)
            alert_toast("An error occurred", "error")
        },
        success: function (resp) {
            if (resp.status == 'success') {
               
                Object.keys(resp.data).map(k => {
                    console.log(resp.data)
                    var data = resp.data[k]
                    var card = $($('noscript#order-clone').html()).clone()
                    card.attr('data-id', data.id)
                    card.find('.queue-code').text(data.queue)
                    card.find('.table-number').text("Table #"+data.table_number)

                    // Set Order Type label styling
                    const typeEl = card.find('.order-type')
                    typeEl.text(data.order_type)
                    if (data.order_type === 'Takeaway') {
                        typeEl.removeClass('bg-secondary').addClass('bg-success')
                    } else {
                        typeEl.removeClass('bg-secondary').addClass('bg-primary')
                    }

                    // Add discount if any
                    if (data.discount) {
                        card.find('.discount-label').html('<div class="discount-badge">' + data.discount + '</div>')
                    }
                  
                    // Add items
                    let itemList = [];
                    Object.keys(data.item_arr).map(i => {
    var item = data.item_arr[i];
    
    var row = $(`
        <div class="d-flex w-100">
            <div class="col-9 m-0">
                <input type="checkbox" class="order-checkbox">
                <span class="item-name">${item.item}</span>
            </div>
            <div class="col-3 m-0 text-center">
                ${parseInt(item.quantity).toLocaleString()}
            </div>
        </div>
    `);

    var checkbox = row.find('.order-checkbox');

    if (item.served == 1) {
        checkbox.prop('checked', true);
        checkbox.prop('disabled', true);
        checkbox.data('served', true);
        row.find('.item-name').addClass('item-served');
        row.find('.col-9').append('<span class="served-label">Served</span>');
        itemList.push(item.item);
    }

    card.find('.order-body').append(row);
});

                    $('#order-field').append(card)

                    card.find('.order-served').click(function () {
                        let itemList = []

                        if (data.order_type === 'Takeaway') {
                            // Auto check and serve all items
                            card.find('.order-checkbox').each(function () {
                                if (!$(this).data('served')) {
                                    $(this).prop('checked', true)
                                    $(this).prop('disabled', true)
                                    $(this).data('served', true)
                                    $(this).siblings('.item-name').addClass('item-served')
                                    $(this).parent().append('<span class="served-label">Served</span>')
                                }
                            })
                        } else {
                            // For Dine-in: serve only checked items
                            card.find('.order-checkbox').each(function (index) {
                                if (this.checked && !$(this).data('served')  ) {
                                    $(this).data('served', true)
                                    $(this).prop('disabled', true)
                                    $(this).siblings('.item-name').addClass('item-served')
                                    $(this).parent().append('<span class="served-label">Served</span>')
                                    itemList.push($(this).siblings('.item-name').text())
                                    const id = data.item_arr[index].id
                                    serve_one_order(id)
                                }
                            })
                        }

                        // Check if all items served
                        let allServed = true;
                        card.find('.order-checkbox').each(function () {
                            if (!$(this).data('served')) {
                                allServed = false;
                              
                            }
                        
                        })

                        if (allServed) {
                            serve_order(data.id)
                        }
                    })
                })
            }
        }
    })
}

$(function () {
    $('body').addClass('sidebar-collapse')
    setInterval(get_order, 500)
})


    function serve_order($id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=serve_order",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    alert_toast("Order has been fully served.", 'success');
                    $('.order-item[data-id="' + $id + '"]').remove()
                } else {
                    alert_toast("An error occurred.", 'error');
                }
                end_loader();
            }
        })
    }

        function serve_one_order($id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=serve_one_order",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    alert_toast("Order has been fully served.", 'success');
                    $('.order-item[data-id="' + $id + '"]').remove()
                } else {
                    alert_toast("An error occurred.", 'error');
                }
                end_loader();
            }
        })
    }
</script>

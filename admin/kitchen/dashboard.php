<style>
    /* Scrollbar Styling */
    #order-field, .order-list {
        scrollbar-width: thin;
        scrollbar-color: #ffc107 #f1f1f1;
    }

    #order-field::-webkit-scrollbar, .order-list::-webkit-scrollbar {
        width: 6px;
    }

    #order-field::-webkit-scrollbar-thumb, .order-list::-webkit-scrollbar-thumb {
        background-color: #ffc107;
        border-radius: 10px;
    }

    /* Order Field Area */
    #order-field {
        height: 54em;
        overflow-y: auto;
        padding: 0.5em;
        background: linear-gradient(135deg, #fff8dc, #fff3c0);
        border-radius: 10px;
    }

    /* Empty State */
    #order-field:empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fffaf0;
        border-radius: 10px;
    }

    #order-field:empty:after {
        content: "No order has been queued yet.";
        color: #b7b4b4;
        font-size: 1.7em;
        font-style: italic;
    }

    /* Order List */
    .order-list {
        height: 18em;
        overflow-y: auto;
        position: relative;
        border-radius: 8px;
        background: #fffbea;
        border: 1px solid #f7e8a2;
    }

    .order-list-header {
        position: sticky;
        top: 0;
        z-index: 2 !important;
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #000;
        padding: 0.4em;
        font-weight: bold;
        border-bottom: 1px solid #e6c200;
    }

    /* Order Item Row */
    .order-body > .d-flex {
        padding: 0.4em;
        border-bottom: 1px solid #f0e3a2;
        align-items: center;
    }

    .order-body > .d-flex:last-child {
        border-bottom: none;
    }

    /* Card Styling */
    .card.rounded-0.shadow.card-outline.card-warning {
        border: 2px solid #ffc107;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        background: #fffdf7;
    }

    .card-header {
        background: linear-gradient(45deg, #fff8dc, #fff3c2);
        border-bottom: 1px solid #ffe484;
        font-size: 1.1em;
    }

    .card-title {
        color: #ff9c00;
    }

    /* Serve Button */
    .serve-button {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #000;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .serve-button:hover {
        background: linear-gradient(45deg, #ffb300, #ff6f00);
        transform: scale(1.03);
    }

    /* Served Item Styles */
    .order-served {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #000;
        font-weight: 600;
        transition: 0.3s ease-in-out;
    }

    .order-served:hover {
        background: linear-gradient(45deg, #ffb300, #ff6f00);
        transform: scale(1.03);
    }

    .served-label {
        color: green;
        font-size: 0.9em;
        font-weight: bold;
        margin-left: 8px;
    }

    .item-served {
        text-decoration: line-through;
        color: #6c757d;
    }

    .order-checkbox {
        margin-right: 0.5em;
        transform: scale(1.2);
    }

    .order-item .d-flex > .col-9 {
        display: flex;
        align-items: center;
    }

    /* Discount Badge */
    .discount-badge {
        display: inline-block;
        padding: 2px 10px;
        font-size: 0.8em;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(90deg, #ffc107, #ff9800);
        border-radius: 30px;
        margin-top: 5px;
    }

    /* Category Label */
    .category-label {
        margin-top: 1rem;
        font-weight: bold;
        font-size: 0.95rem;
        color: #000;
        padding: 0.3rem 0.5rem;
        background: linear-gradient(90deg, #ffc107, #ff9800);
        border-radius: 6px;
        display: inline-block;
    }

    /* General UI Enhancement */
    body {
        background: linear-gradient(90deg, #fff8e1, #fff3c0);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .kitchen-header-gradient {
    background: linear-gradient(90deg, #ffc107, #ff9800);
    color: #fff;
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
            <div class="card-header py-2 text-center">
                <div class="card-title mb-0">
                    <b>Queue Code: 10001</b>
                    <div class="discount-label"></div>
                </div>
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
    function get_order(){
        let listed = []
        $('.order-item').each(function(){
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
            success: function(resp) {
                if (resp.status == 'success') {
                    Object.keys(resp.data).map(k => {
                        var data = resp.data[k]
                        var card = $($('noscript#order-clone').html()).clone()
                        card.attr('data-id', data.id)
                        card.find('.card-title b').text('Queue #' + data.queue)

                        if (data.discount) {
                            card.find('.discount-label').html('<div class="discount-badge">' + data.discount + '</div>')
                        }

                        Object.keys(data.item_arr).map(i => {
                            var item = data.item_arr[i]
                            var row = $('<div class="d-flex w-100">\
                                <div class="col-9 m-0">\
                                    <input type="checkbox" class="order-checkbox">\
                                    <span class="item-name">' + item.item + '</span>\
                                </div>\
                                <div class="col-3 m-0 text-center">' + parseInt(item.quantity).toLocaleString() + '</div>\
                            </div>')
                            card.find('.order-body').append(row)
                        })

                        $('#order-field').append(card)

                        card.find('.order-served').click(function() {
                            let itemList = []
                            card.find('.order-checkbox').each(function(){
                                if(this.checked && !$(this).data('served')){
                                    $(this).data('served', true)
                                    $(this).prop('disabled', true)
                                    $(this).siblings('.item-name').addClass('item-served')
                                    $(this).parent().append('<span class="served-label">Served</span>')
                                    itemList.push($(this).siblings('.item-name').text())
                                }
                            })

                            let allServed = true;
                            card.find('.order-checkbox').each(function(){
                                if(!$(this).data('served')){
                                    allServed = false;
                                }
                            })

                            if(allServed){
                                serve_order(data.id)
                            }
                        })
                    })
                }
            }
        })
    }

    $(function(){
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
</script>

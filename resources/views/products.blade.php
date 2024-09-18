<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Product Management</h1>
        <form id="productForm" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity in Stock</label>
                <input type="number" class="form-control" id="quantity" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price per Item</label>
                <input type="number" step="0.01" class="form-control" id="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>Datetime Submitted</th>
                    <th>Total Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="productList">
                @foreach ($products as $product)
                    <tr data-id="{{ $product['id'] }}">
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['quantity'] }}</td>
                        <td>{{ $product['price'] }}</td>
                        <td>{{ $product['datetime'] }}</td>
                        <td>{{ $product['quantity'] * $product['price'] }}</td>
                        <td><button class="btn btn-sm btn-primary edit-btn">Edit</button></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total</td>
                    <td id="grandTotal"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function updateGrandTotal() {
                let total = 0;
                $('#productList tr').each(function() {
                    total += parseFloat($(this).find('td:eq(4)').text());
                });
                $('#grandTotal').text(total.toFixed(2));
            }

            updateGrandTotal();

            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/products',
                    method: 'POST',
                    data: {
                        name: $('#name').val(),
                        quantity: $('#quantity').val(),
                        price: $('#price').val(),
                    },
                    success: function(response) {
                        let newRow = `
                            <tr data-id="${response.id}">
                                <td>${response.name}</td>
                                <td>${response.quantity}</td>
                                <td>${response.price}</td>
                                <td>${response.datetime}</td>
                                <td>${(response.quantity * response.price).toFixed(2)}</td>
                                <td><button class="btn btn-sm btn-primary edit-btn">Edit</button></td>
                            </tr>
                        `;
                        $('#productList').append(newRow);
                        $('#productForm')[0].reset();
                        updateGrandTotal();
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                let row = $(this).closest('tr');
                let id = row.data('id');
                let name = row.find('td:eq(0)').text();
                let quantity = row.find('td:eq(1)').text();
                let price = row.find('td:eq(2)').text();

                row.html(`
                    <td><input type="text" class="form-control" value="${name}"></td>
                    <td><input type="number" class="form-control" value="${quantity}"></td>
                    <td><input type="number" step="0.01" class="form-control" value="${price}"></td>
                    <td>${row.find('td:eq(3)').text()}</td>
                    <td>${row.find('td:eq(4)').text()}</td>
                    <td><button class="btn btn-sm btn-success save-btn">Save</button></td>
                `);
            });

            $(document).on('click', '.save-btn', function() {
                let row = $(this).closest('tr');
                let id = row.data('id');
                let name = row.find('input:eq(0)').val();
                let quantity = row.find('input:eq(1)').val();
                let price = row.find('input:eq(2)').val();
                $.ajax({
                    url: `/products/${id}`,
                    method: 'PUT',
                    data: {
                        name: name,
                        quantity: quantity,
                        price: price,
                    },
                    success: function(response) {
                        row.html(`
                            <td>${response.name}</td>
                            <td>${response.quantity}</td>
                            <td>${response.price}</td>
                            <td>${response.datetime}</td>
                            <td>${(response.quantity * response.price).toFixed(2)}</td>
                            <td><button class="btn btn-sm btn-primary edit-btn">Edit</button></td>
                        `);
                        updateGrandTotal();
                    }
                });
            });
        });
    </script>
</body>
</html>

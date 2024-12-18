jQuery(document).ready(function ($) {
    toastr.options = {
        "closeButton": true, 
        "progressBar": true, 
        "positionClass": "toast-top-right", 
        "timeOut": 5000, 
        "extendedTimeOut": 0, 
    };

      // Handle search product AJAX
      $(document).on('keyup', '.product-search', function () {
        const searchQuery = $(this).val();
        const slot = $(this).data('slot');

        if (searchQuery.length >= 2) {
            $.ajax({
                url: compare_ajax.ajax_url,
                type: 'GET',
                data: {
                    action: 'search_product',
                    query: searchQuery
                },
                success: function (response) {
                    if (response.success) {
                        const products = response.data.products;
                        let searchResultsHtml = '';

                        if (products.length > 0) {
                            products.forEach(product => {
                                searchResultsHtml += `
                                    <div class="search-result" data-product-id="${product.id}">
                                        <span class="product-title">${product.name}</span> <!-- Product Title -->
                                        <img src="${product.image}" alt="${product.name}" class="product-image" />
                                    </div>`;
                            });
                        } else {
                            searchResultsHtml = '<p>No products found.</p>';
                        }
                        // Display the search results below the search bar
                        $(`.search-results[data-slot="${slot}"]`).html(searchResultsHtml).show();

                    } else {
                        toastr.error(response.data.message);
                    }
                }
            });
        }
    });

    // Handle product selection and add to slot
    $(document).on('click', '.search-result', function () {
        const productId = $(this).data('product-id');
        const slot = $(this).closest('th').find('.product-search').data('slot');

        // Add product to the compare list (AJAX or session update)
        $.post(compare_ajax.ajax_url, {
            action: 'add_to_compare',
            product_id: productId
        }, function (response) {
            if (response.success) {
                toastr.success('Product added to slot ' + (slot + 1));
                location.reload(); // Reload to update the comparison table
            } else {
                toastr.error(response.data.message);
            }
        });

        // Hide the search results after product selection
        $(`.search-results[data-slot="${slot}"]`).hide();
    });

    // Hide search results when user clicks away from the search input
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.product-search').length) {
            $('.search-results').hide();
        }
    });


    $(document).on('click', '.add-to-compare', function (e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        $.post(compare_ajax.ajax_url, {
            action: 'add_to_compare',
            product_id: productId,
        }, function (response) {
            if (response.success) {
                toastr.success(response.data.message);
                location.reload(); 
            } else {
                toastr.error(response.data.message);
            }
        });
    });

    // Browse compare
    $(document).on('click', '.browse-compare', function (e) {
        e.preventDefault();
        window.location.href = compare_ajax.compare_page_url; 
    });

    // Remove from compare
    $(document).on('click', '.remove-from-compare', function (e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        $.post(compare_ajax.ajax_url, {
            action: 'remove_from_compare',
            product_id: productId,
        }, function (response) {
            if (response.success) {
                toastr.success(response.data.message);
                location.reload();
            } else {
                // Use Toastr to show error message
                toastr.error(response.data.message);
            }
        });
    });

    // Clear compare list
    $(document).on('click', '.clear-compare-list', function (e) {
        e.preventDefault();
        $.post(compare_ajax.ajax_url, {
            action: 'clear_compare_list',
        }, function (response) {
            if (response.success) {
                window.location.href = response.data.redirect_url;
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', function () {
    // Listen for input changes
    const inputField = document.querySelector('input'); // Modify to target your specific input field
    const compareButton = document.querySelector('.add-to-compare, .browse-compare');

    if (inputField && compareButton) {
        inputField.addEventListener('input', function () {
            // Get the current input value
            const inputValue = inputField.value.trim();

            // Update the button text based on input
            if (inputValue.length > 0) {
                compareButton.textContent = 'Update Compare'; // Modify as per your logic
            } else {
                compareButton.textContent = compareButton.getAttribute('data-text'); // Reset to default text
            }
        });
    }
});





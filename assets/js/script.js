// Function to toggle the visibility of the product description
function toggleDescription(productId) {
    var description = document.getElementById('description-' + productId);
    var showButton = document.getElementById('show-button-' + productId);
    var hideButton = document.getElementById('hide-button-' + productId);

    if (description.style.display === 'none' || description.style.display === '') {
        description.style.display = 'block';
        showButton.style.display = 'none';
        hideButton.style.display = 'inline';
    } else {
        description.style.display = 'none';
        showButton.style.display = 'inline';
        hideButton.style.display = 'none';
    }
}

// Function to toggle the visibility of the edit form
function toggleEditForm(productId) {
    var editForm = document.getElementById('edit-form-' + productId);
    if (editForm.style.display === 'none' || editForm.style.display === '') {
        editForm.style.display = 'table-row';
    } else {
        editForm.style.display = 'none';
    }
}



// showOngoingAuctions.php 
function showOngoingAuctions() {
    var rows = document.getElementsByClassName('auction-row');
    for (var i = 0; i < rows.length; i++) {
        if (rows[i].getAttribute('data-status') === 'ongoing') {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

function showUpcomingAuctions() {
    var rows = document.getElementsByClassName('auction-row');
    for (var i = 0; i < rows.length; i++) {
        if (rows[i].getAttribute('data-status') === 'upcoming') {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}



// bid_product.php 
function validateBidAmount() {
    var bidAmount = document.getElementById('bid_amount').value;
    var currentPrice = document.getElementById('current-price').textContent;
    
    if (parseFloat(bidAmount) <= parseFloat(currentPrice)) {
        alert('Bid amount must be greater than the current price.');
        return false;
    }
    return true;
}

// a_manage_auction.php
function toggleEditForm(auctionId) {
    var form = document.getElementById('edit-form-' + auctionId);
    if (form.classList.contains('show-form')) {
      form.classList.remove('show-form');
    } else {
      form.classList.add('show-form');
    }
  }

<?php

// Tables
function getStatus() {
    return ['occupied', 'available', 'reserved', 'out of service'];
}

function getColorForStatus($status) {
    switch ($status) {
        case 'occupied':
            return 'red';
        case 'available':
            return 'green';
        case 'reserved':
            return 'yellow';
        case 'out of service':
            return 'gray';
        default:
            return '';
    }
}
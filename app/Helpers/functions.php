<?php

// Tables
function getStatus()
{
    return ['occupied', 'available', 'reserved', 'out of service'];
}

function getColorForTableStatus($status)
{
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

function getColorForOrderStatus($status)
{
    switch ($status) {
        case 'pending':
            return 'red';
        case 'preparing':
            return 'orange';
        case 'served':
            return 'green';
        case 'paid':
            return 'blue';
        case 'reserved':
            return 'yellow';
        case 'cancelled':
            return 'gray';
        default:
            return '';
    }
}

function getNextStatus($status)
{
    switch ($status) {
        case 'pending':
            return 'preparing';
        case 'preparing':
            return 'served';
        case 'served':
            return 'paid';
        default:
            return '';
    }
}
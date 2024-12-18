<?php

namespace PCIW;

function get_compare_session() {
    if (!session_id()) {
        session_start();
    }
    return isset($_SESSION['compare_products']) ? $_SESSION['compare_products'] : [];
}

function set_compare_session($data) {
    if (!session_id()) {
        session_start();
    }
    $_SESSION['compare_products'] = $data;
}

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <title>Inicio</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?= URL . '/css/bootstrap.min.css' ?>">
        <script src="<?= URL . '/js/bootstrap.bundle.min.js' ?>"></script>
        <script src="<?= URL . '/js/color-modes.js' ?>"></script>
        <meta name="theme-color" content='#712cf9'>
        <link href="<?= URL . '/css/sidebar.css' ?>" rel="stylesheet">
        <style>
            .bd-placeholder-img {
                font-size:1.125rem;
                text-anchor:middle;
                -webkit-user-select:none;
                -moz-user-select:none;
                user-select:none
            }
            
            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size:3.5rem
                }
            }
            
            .b-example-divider {
                width:100%;
                height:3rem;
                background-color:#0000001a;
                border:solid rgba(0,0,0,.15);
                border-width:1px 0;
                box-shadow:inset 0 .5em 1.5em #0000001a,inset 0 .125em .5em #00000026
            }
            
            .b-example-vr {
                flex-shrink:0;
                width:1.5rem;
                height:100vh
            }
            
            .bi {
                vertical-align:-.125em;
                fill:currentColor
            }
            
            .nav-scroller {
                position:relative;
                z-index:2;
                height:2.75rem;
                overflow-y:hidden
            }
            
            .nav-scroller .nav {
                display:flex;
                flex-wrap:nowrap;
                padding-bottom:1rem;
                margin-top:-1px;
                overflow-x:auto;
                text-align:center;
                white-space:nowrap;
                -webkit-overflow-scrolling:touch
            }
            
            .btn-bd-primary {
                --bd-violet-bg: #712cf9;
                --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
                --bs-btn-font-weight: 600;
                --bs-btn-color: var(--bs-white);
                --bs-btn-bg: var(--bd-violet-bg);
                --bs-btn-border-color: var(--bd-violet-bg);
                --bs-btn-hover-color: var(--bs-white);
                --bs-btn-hover-bg: #6528e0;
                --bs-btn-hover-border-color: #6528e0;
                --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
                --bs-btn-active-color: var(--bs-btn-hover-color);
                --bs-btn-active-bg: #5a23c8;--bs-btn-active-border-color: #5a23c8
            }
            
            .bd-mode-toggle {
                z-index:1500
            }
            
            .bd-mode-toggle .bi {
                width:1em;
                height:1em
            }
            
            .bd-mode-toggle .dropdown-menu .active .bi {
                display:block!important
            }
        </style>
    </head>
    <body id="body" class="bg-light text-dark d-flex flex-column min-vh-100">
        <main class="d-flex flex-nowrap" style="min-height: 100vh">
            <h1 class="visually-hidden">Barra inicio</h1>
            <div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 bg-light text-dark" style="width: 289px;">
                <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none" href="/">
                    <img id="logo" src="<?= URL . '/img/logo_oscuro.png' ?>" data-logo-light="<?= URL . '/img/logo_claro.png' ?>"
                    data-logo-dark="<?= URL . '/img/logo_oscuro.png' ?>" alt="logo" width="240" height="80" class="pe-none me-2">
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" aria-current="page">
                            <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
                                <use xlink:href="#home">
                                    <symbol id="home" viewBox="0 0 16 16">
                                        <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"></path>
                                    </symbol>
                                </use>
                            </svg>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a class="nav-link text-dark" href="#">
                            <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
                                <use xlink:href="#speedometer2">
                                    <symbol id="speedometer2" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4zM3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z"></path>
                                        <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A7.988 7.988 0 0 1 0 10zm8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3z"></path>
                                    </symbol>
                                </use>
                            </svg>
                            Analiticas
                        </a>
                    </li>
                    <li>
                        <a class="nav-link text-dark" href="#">
                            <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
                                <use xlink:href="#table">
                                    <symbol id="table" viewBox="0 0 16 16">
                                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"></path>
                                    </symbol>
                                </use>
                            </svg>
                            Registrar permiso
                        </a>
                    </li>
                    <li>
                        <a class="nav-link text-dark" href="#">
                            <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
                                <use xlink:href="#grid">
                                    <symbol id="grid" viewBox="0 0 16 16">
                                        <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"></path>
                                    </symbol>
                                </use>
                            </svg>
                            Registrar chofer
                        </a>
                    </li>
                    <li>
                        <a class="nav-link text-dark" href="#">
                            <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
                                <use xlink:href="#people-circle">
                                    <symbol id="people-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"></path>
                                    </symbol>
                                </use>
                            </svg>
                            C
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="rounded-circle me-2" src="https://github.com/mdo.png" alt="" width="32" height="32">
                        <strong>Galo Orellana</strong>
                    </a>
                </div>
            </div>
        </main>

<?php require_once APP . '/views/inc/footer.php' ?>
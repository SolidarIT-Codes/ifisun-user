<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//var_dump($assetsUrl);exit;
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Ifisun | Enregistrer votre plainte">
    <meta name="author" content="Ifisun">
    <title>Ifisun | Enregistrer votre plainte</title>

    <!-- Favicons-->
    <link rel="shortcut icon" href="<?= $assetsUrl ?>img/ifisun-logo.png" type="image/x-icon">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,600" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="<?= $assetsUrl ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assetsUrl ?>css/menu.css" rel="stylesheet">
    <link href="<?= $assetsUrl ?>css/style.css" rel="stylesheet">
    <link href="<?= $assetsUrl ?>css/vendors.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="<?= $assetsUrl ?>css/custom.css" rel="stylesheet">

    <!-- MODERNIZR MENU -->
    <script src="<?= $assetsUrl ?>js/modernizr.js"></script>

</head>

<body>
<style>
    .content-left {
        background-color: #ffffff;
        padding: 0;
    }

    .content-left-wrapper {
        background: transparent !important;
    }

    .black-color {
        color: #000000 !important;
    }

    .white-color {
        color: #ffffff !important;
    }

    .main-bg {
        background-color: #434bdf;
        background: #434bdf!important;
    }
    .backward:hover, .forward:hover{
        background-color: #ffffff!important;
        color: #000000!important;
    }
</style>
<div id="preloader">
    <div data-loader="circle-side"></div>
</div><!-- /Preload -->

<div id="loader_form">
    <div data-loader="circle-side-2"></div>
</div><!-- /loader_form -->

<nav>
    <ul class="cd-primary-nav">
        <li><a href="/ifi" class="animated_link">Se plaindre</a></li>
        <li><a href="denonce" class="animated_link">Dénoncer</a></li>
         <li><a href="temoigner" class="animated_link">Témoigner</a></li>
        <li><a target="_blank" href="https://www.solidarit-hub.org" class="animated_link">SolidarIT</a></li>
        <li><a target="_blank" href="http://localhost/ifpro/" class="animated_link">Admin</a></li>
    </ul>
</nav>
<!-- /menu -->

<div class="container-fluid full-height">
    <div class="row row-height">
        <div class="col-lg-6 content-left">
            <div class="content-left-wrapper">
                <a href="index.html" id="logo"><img src="<?= $assetsUrl ?>img/ifisun-logo.png" alt="" width="69"></a>
                <div id="social">
                    <ul>
                        <li><a style="background: #395693;" href="#0"><i class="icon-facebook"></i></a></li>
                        <li><a style="background: #1c9cea;" href="#0"><i class="icon-twitter"></i></a></li>
                        <li><a style="background: #d54836;" href="#0"><i class="icon-google"></i></a></li>
                        <li><a style="background: #0270ad;" href="#0"><i class="icon-linkedin"></i></a></li>
                    </ul>
                </div>
                <!-- /social -->
                <div class="black-color">
                    <figure><img src="<?= $assetsUrl ?>img/fighting.png" alt="" class="img-fluid"></figure>
                    <h2 class="black-color">Témoigner d'un acte de violence</h2>
                    <p>Tation argumentum et usu, dicit viderer evertitur te has. Eu dictas concludaturque usu, facete
                        detracto patrioque an per, lucilius pertinacia eu vel. Adhuc invidunt duo ex. Eu tantas dolorum
                        ullamcorper qui.</p>
                    <a href="#start" class="btn_1 rounded mobile_btn">Déposer une plainte</a>
                </div>
                <div class="copy black-color">© 2018 Ifisun</div>
            </div>
            <!-- /content-left-wrapper -->
        </div>
        <!-- /content-left -->

        <div class="col-lg-6 content-right main-bg" id="start">
            <div id="wizard_container">
                <div id="top-wizard">
                    <div id="progressbar"></div>
                </div>
                <!-- /top-wizard -->
                <?php
                echo form_open();
                ?>
                <input id="website" name="website" type="text" value="">
                <!-- Leave for security protection, read docs for details -->
                <div id="middle-wizard">
                    <div class="step">
                        <h3 class="main_question white-color"><strong class="white-color">1/2</strong>Veillez remplir le
                            formulaire ci-après avec exactitude</h3>
                        <div class="form-group">
                            <?php
                            echo form_input([
                                'name' => 'complain[last_name]',
                                'placeholder' => 'Nom',
                                'class' => 'form-control required'
                            ]);
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo form_input([
                                'name' => 'complain[first_name]',
                                'placeholder' => 'Prénom',
                                'class' => 'form-control required'
                            ]);
                            ?>
                        </div>
                        <div class="form-group">
                              <?php
                                    echo form_input([
                                        'name' => 'complain[age]',
                                        'placeholder' => 'Age',
                                        'class' => 'form-control required',
                                        'type' => 'number'
                                    ]);
                                    ?>
                        </div>

                            
                                <div class="form-group">
                                    <?php
                                    echo form_input([
                                        'name' => 'complain[profession]',
                                        'placeholder' => 'Profession',
                                        'class' => 'form-control required',
                                        'type' => 'text'
                                    ]);
                                    ?>
                                </div>
                            
                        <!-- /row -->
                        <div class="form-group terms">
                            <label class="container_check white-color">Veuillez accepter nos
                             <a href="#"
                                                                                                data-toggle="modal"
                                                                                                style="color: wheat"
                                                                                                data-target="#terms-txt">conditions
                                    d'utilisation</a>
                                <input type="checkbox" name="terms" value="Yes" class="required">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <!-- /step-->
                    <div class="submit step">                        
                        <div class="form-group">
                            <?php
                            echo form_textarea([
                                'name'=>'complain[text]',
                                'placeholder'=>'Descrivez les faits',
                                'class'=>'form-control required',
                            ])
                            ?>
                        </div>

                    </div>
                    <!-- /step-->
                    <!-- /step-->
                    <!-- /step-->
                </div>
                <!-- /middle-wizard -->
                <div id="bottom-wizard">
                    <button type="button" name="backward" class="backward main-bg white-color">Précédent</button>
                    <button type="button" name="forward" class="forward white-color">Suivant
                    </button>
                    <button type="submit" name="process" class="submit white-color">Soumettre</button>
                </div>
                <!-- /bottom-wizard -->
                <?php echo form_close() ?>
            </div>
            <!-- /Wizard container -->
        </div>
        <!-- /content-right-->
    </div>
    <!-- /row-->
</div>
<!-- /container-fluid -->

<div class="cd-overlay-nav">
    <span></span>
</div>
<!-- /cd-overlay-nav -->

<div class="cd-overlay-content">
    <span></span>
</div>
<!-- /cd-overlay-content -->

<a href="#0" class="cd-nav-trigger">Menu<span class="cd-icon"></span></a>
<!-- /menu button -->

<!-- Modal terms -->
<div class="modal fade" id="terms-txt" tabindex="-1" role="dialog" aria-labelledby="termsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="termsLabel">Conditions d'utilisation</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Lorem ipsum dolor sit amet, in porro albucius qui, in <strong>nec quod novum accumsan</strong>, mei
                    ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne
                    quod dicunt sensibus.</p>
                <p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam
                    dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt
                    sensibus. Lorem ipsum dolor sit amet, <strong>in porro albucius qui</strong>, in nec quod novum
                    accumsan, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum
                    sanctus, pro ne quod dicunt sensibus.</p>
                <p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam
                    dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt
                    sensibus.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn_1" data-dismiss="modal">Fermer</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- COMMON SCRIPTS -->
<script src="<?= $assetsUrl ?>js/jquery-3.2.1.min.js"></script>
<script src="<?= $assetsUrl ?>js/common_scripts.min.js"></script>
<script src="<?= $assetsUrl ?>js/velocity.min.js"></script>
<script src="<?= $assetsUrl ?>js/functions.js"></script>

<!-- Wizard script -->
<script src="<?= $assetsUrl ?>js/survey_func.js"></script>

</body>

<!-- Mirrored from www.ansonika.com/wilio/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Dec 2018 12:10:58 GMT -->
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Document</title>
    <link href="{{ public_plugins('datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('admin-resources/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: var(--ct-modal-content-bg);
            background-clip: padding-box;
            border: 1px solid transparent;
            border-radius: 0.2rem;
            outline: 0;
        }

        .row {
            --ct-gutter-x: 1.5rem;
            --ct-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--ct-gutter-y));
            margin-right: calc(-.5 * var(--ct-gutter-x));
            margin-left: calc(-.5 * var(--ct-gutter-x))
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--ct-gutter-x) * .5);
            padding-left: calc(var(--ct-gutter-x) * .5);
            margin-top: var(--ct-gutter-y)
        }

        .col {
            flex: 1 0 0%
        }

        .col-lg-12 {
            flex: 0 0 auto;
            width: 100%;
        }

        .col-md-12 {
            flex: 0 0 auto;
            width: 100%
        }

        .col-sm-12 {
            flex: 0 0 auto;
            width: 100%;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.45rem 0.9rem;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.5;
            color: #6c757d;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            appearance: none;
            border-radius: 0.2rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #6c757d;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.45rem 0.9rem;
            font-size: 0.9rem;
            border-radius: 0.15rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            color: #fff;
            background-color: #5c636a;
            border-color: #565e64;
        }

        .btn-warning {
            color: #343a40;
            background-color: #f9c851;
            border-color: #f9c851;
        }

        .btn-warning:hover {
            color: #343a40;
            background-color: #fad06b;
            border-color: #face62;
        }

        .text-center {
            text-align: center !important;
        }

        label {
            display: inline-block
        }

        button {
            border-radius: 0
        }

        button:focus:not(:focus-visible) {
            outline: 0
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit
        }

        [type=button]:not(:disabled),
        [type=reset]:not(:disabled),
        [type=submit]:not(:disabled),
        button:not(:disabled) {
            cursor: pointer
        }

        .addbodyparts {
            display: none;
        }

        canvas {
            pointer-events: none;
            position: absolute;
        }

        audio,
        canvas,
        progress,
        video {
            display: inline-block;
            vertical-align: baseline;
        }

        .imgmap_css_container {
            height: 250px !important;
            width: 250px !important;
        }

        .col-lg-4,
        .col-md-4,
        .col-sm-4 {
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }



        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .fishbone-container {
            display: inline-grid;
            grid-template-columns: repeat(4, auto);
            grid-template-rows: auto .2em auto;
            padding-left: 2em;
            font-family: Arial;
            --bone-color: #85A0B2;
            --yellow: #FDBE22;
            --green: #69E982;
            --blue: #5CB2FB;
        }

        .cause {
            display: flex;
            flex-direction: column;
            transform: skew(20deg);
            transform-origin: bottom;
            margin-left: .8em;
        }

        .rootcause {
            text-align: center;
            position: relative;
            left: 100%;
            transform: translateX(-50%) skewX(-20deg);
            font-size: 1.5em;
            color: #fff;
            padding: .2em;
            border-radius: .2em;

            &.yellow {
                background-color: var(--yellow);
            }

            &.green {
                background-color: var(--green);
            }

            &.blue {
                background-color: var(--blue);
            }
        }

        .subcause {
            flex-grow: 1;
            border-right: .2em solid var(--bone-color);
            padding-bottom: .75em;
            padding-top: .75em
        }

        .stat {
            text-align: right;
            padding-right: 3em;
            position: relative;
            transform: skewX(-20deg);
            line-height: 1.5em;
            font-size: 1em;
        }

        .stat:before {
            content: '';
            display: block;
            background-color: var(--bone-color);
            position: absolute;
            width: 3em;
            height: .2em;
            right: 0;
            top: 50%;
            transform: translate(.2em, -50%);
        }

        .line {
            grid-column-start: 1;
            grid-column-end: 4;
            background-color: var(--bone-color);

            ~.cause {
                transform: skewX(-20deg);
                transform-origin: top;
            }

            ~.cause .rootcause {
                transform: translateX(-50%) skewX(20deg);
            }

            ~.cause .stat {
                transform: skewX(20deg);
            }
        }

        .defect-spacer-top {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 1;
            grid-row-end: 2;
        }

        .defect {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 2;
            grid-row-end: 3;
        }

        .defect-spacer-bottom {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 3;
            grid-row-end: 4;
        }

        .defect-text {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            padding: 1em;
            margin-left: .5em;
            background-color: var(--bone-color);
            border-radius: .5em;
            color: #fff;
            text-align: center;
        }

        .subcause .stat {
            margin-bottom: 15px;
            /* Adjust the spacing between input fields */
        }

        .subcause {
            margin-bottom: 20px;
            /* Add spacing between rows of input fields */
        }
    </style>
</head>


<body>
    @php
        $is_ready_only = '';
    @endphp

    <div id="injury_model" class="modal  fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-body modal-pic ">
                    <!-- model content here -->
                    <!-- invetigation form start-->


                    <form id="injuryform" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="humanbodyinjury" id="humanbodyinjury1">
                        <input type="hidden" name="humanbodyinjurylabel" id="humanbodyinjurylabel1">
                        <input type="hidden" name="body_prim_id" id="body_prim_id" value="">
                        <input type="hidden" name="incident_id" id="incident_id" value="">
                        <input type="hidden" name="injury_id" id="injury_id" value="">
                        <input type="hidden" name="bodypartimage" id="bodypartimage">
                        <input type="hidden" name="random_id" id="random_id" value="{{ $randomID }}">
                        <input type="hidden" name="row_id" id="row_id" value="{{ $rowId }}">
                        <input type="hidden" name="injury_person_type" id="injury_person_type"
                            value="{{ $injury_person_type }}">
                        <input type="hidden" name="injuredPerson" id="injuredPerson" value="{{ $injured_person_id }}">
                        <div class="container-fluid1">

                            <div class="box-body1 box-group">

                                <div class="box">

                                    <!-- /.box-header -->
                                    <div class="box-body">

                                        <div class="bodyparts">
                                            <div class="row"
                                                style="{{ $is_ready_only == 1 ? 'pointer-events: none;' : '' }}">

                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <input type="hidden" name="imgMapdata" id="imgMapdata1">

                                                    <div class="img-content col-lg-4 col-md-4 col-sm-4"
                                                        data-type='img-full'>
                                                        <div class="img-map">


                                                        </div>
                                                        <!--Male total parts-->
                                                        <script type="text/template" id="tmp-male">

                                                                <div class="img-wrap male">
                                                <div class="canvas">
                                                <canvas id='image1_canvas'></canvas>
                                                <canvas id='image1_canvas_marked'></canvas>
                                                </div>
                                                <img src="{{ admin_url('public/assets/images/human_body_parts/male/full.png') }}"  usemap='#imgmap_1' class='imgmap_1' title='imgmap1' alt='imgmap1' id='img-imgmap1' />
                                                <map id='imgmap1' name='imgmap_1' data-type='total' data-map="total">
                                                <area alt="" data-parentid = "0" data-isparent="1"  onclick="changeImage('.male-face')"  title="Head" data-map="one"  shape="poly" coords="63,45,72,43,80,43,85,47,88,51,88,57,88,61,90,63,90,67,87,70,85,71,85,74,85,77,82,79,80,81,76,81,64,81,62,80,61,74,60,72,58,70,57,67,57,62,57,58,57,54,59,50,59,48" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()"  title="Neck" data-map="Two" shape="poly" coords="62,82,82,81,81,91,63,90" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Shoulder Front" data-map="three"  shape="poly" coords="62,88,73,91,72,107,28,108,34,99,42,96,49,96,57,94,59,93" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right shoulder Front" data-map="four"   shape="poly" coords="72,90,82,90,85,93,91,96,95,96,100,96,107,99,112,101,115,104,116,108,72,108" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Arm Front" data-map="five"  shape="poly" coords="28,108,48,107,46,110,43,118,43,129,41,150,26,146,27,129" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Arm Front" data-map="six"  shape="poly" coords="100,107,116,108,119,114,119,120,120,125,120,131,121,138,122,146,105,150,103,125,102,117,102,113" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Elbow Front" data-map="seven"  shape="poly" coords="26,145,40,150,40,157,39,164,22,156,24,151" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Elbow Front " data-map="eight"  shape="poly" coords="104,149,122,146,122,150,125,156,125,159,106,161" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Forearm Front" data-map="nine"  shape="poly" coords="21,155,39,163,36,172,33,179,28,192,25,195,17,190,18,181,19,174,19,167" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Forearm Front" data-map="ten"  shape="poly" coords="107,161,124,158,126,164,126,169,126,174,127,184,128,191,121,195,111,178" />
                                                <area alt="" data-parentid = "0" data-isparent="1"  onclick="changeImage('.male-hand-right')" title="Right Hand " data-map="eleven"  shape="poly" coords="16,193,25,196,27,205,24,212,24,220,20,223,18,224,15,226,12,225,9,222,10,212,9,207,6,207,5,203,11,196" />
                                                <area alt="" data-parentid = "0" data-isparent="1"  onclick="changeImage('.male-hand-left')" title="Left Hand" data-map="twelve"  shape="poly" coords="119,194,129,190,132,193,136,196,140,201,141,204,140,206,136,204,136,209,136,215,135,221,132,224,126,224,122,219,120,210" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Chest" data-map="thirteen"  shape="poly" coords="46,108,44,115,43,125,43,135,72,135,71,108" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Chest" data-map="fourteen"  shape="poly" coords="72,107,102,109,101,135,72,137" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Stomach" data-map="fifteen"  shape="poly" coords="43,135,102,135,101,143,99,145,98,150,98,155,97,160,97,166,73,166,49,166,48,150" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title=" Left Hip" data-map="sixteen"  shape="poly" coords="48,166,72,166,72,189,45,189,48,173" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Hip" data-map="seventeen"  shape="poly" coords="72,166,97,166,97,173,101,180,101,189,72,190" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Pubis" data-map="eighteen"  shape="poly" coords="43,189,101,189,103,210,75,213,70,214,42,210,43,198" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Thigh" data-map="nineteen"  shape="poly" coords="74,214,103,210,103,226,103,237,102,243,100,248,96,257,78,259,75,239" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Thigh" data-map="twenty"  shape="poly" coords="49,259,66,259,69,253,69,247,69,239,69,230,71,215,42,211,42,235" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Knee" data-map="twentyone"  shape="poly" coords="79,258,97,258,94,268,92,274,86,276,81,276" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Knee" data-map="twentytwo"  shape="poly" coords="49,259,67,259,66,265,64,269,62,273,60,275,57,277,52,265" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Leg "  data-map="twentythree"  shape="poly" coords="81,267,87,277,91,277,93,271,96,277,99,284,99,292,96,302,93,314,90,326,81,326,77,302,77,292" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Leg " data-map="twentyfour"  shape="poly" coords="59,276,67,264,68,277,68,284,69,291,69,297,69,305,69,314,68,321,67,326,57,326,53,314,50,300,48,286,49,278,52,274,51,266,51,262" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Ankle" data-map="twentyfive"  shape="poly" coords="56,326,67,326,67,343,55,341,58,334" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Ankle" data-map="twentysix"  shape="poly" coords="80,327,89,327,92,345,80,343,78,345" />
                                                <area alt="" data-parentid = "0" data-isparent="1"  onclick="changeImage('.male-foot-left')" title="Left foot" data-map="twentyseven"  shape="poly" coords="79,342,91,342,98,350,100,354,99,357,85,357,78,357,78,353,76,350" />
                                                <area alt="" data-parentid = "0" data-isparent="1"  onclick="changeImage('.male-foot-right')" title="Right Foot" data-map="twentyeight"  shape="poly" coords="54,342,66,342,69,353,69,356,58,356,47,356" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Back Skull" data-map="twentynine"  shape="poly" coords="199,77,196,77,196,73,196,70,198,67,197,63,199,57,201,54,205,51,209,50,214,50,219,51,224,54,227,59,227,64,227,67,229,71,228,75,225,79,223,84,218,86,210,86,202,86" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Back Neck" data-map="thirty"  shape="poly" coords="201,85,209,87,219,87,221,97,202,97" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Shoulder Back" data-map="thirtyone"  shape="poly" coords="201,96,212,96,212,121,167,122,166,118,170,110,176,106,183,102,193,102" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Shoulder Back" data-map="thirtytwo"  shape="poly" coords="211,96,222,96,226,100,234,102,240,103,246,105,253,109,256,115,256,121,212,121" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Arm Back" data-map="thirtythree"  shape="poly" coords="167,120,184,121,182,128,184,143,179,163,163,156,168,136" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Arm back" data-map="thirtyfour"  shape="poly" coords="241,121,257,122,261,156,245,163,239,145" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Elbow back" data-map="thirtyfive"  shape="poly" coords="164,156,181,162,177,174,161,166" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Elbow back" data-map="thirtysix"  shape="poly" coords="244,161,261,155,264,162,264,166,248,171,245,168" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Arm Back " data-map="thirtyseven"  shape="poly" coords="161,164,177,174,167,198,157,194,160,185,159,174" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Arm Back" data-map="thirtyeight"  shape="poly" coords="247,170,263,164,265,185,267,192,267,195,257,198,251,182,247,174" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Upper Back " data-map="thirtynine"  shape="poly" coords="183,122,242,121,242,128,241,136,241,143,239,147,238,153,237,155,187,156,184,148,181,129" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Lower Back"   data-map="forty"  shape="poly" coords="187,154,237,154,237,175,240,188,186,187,187,176" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Buttock"  data-map="fortyone"  shape="poly" coords="185,188,211,188,213,212,209,215,208,218,181,217,183,202,184,199,184,195" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Buttock"  data-map="fortytwo" shape="poly" coords="211,213,211,188,239,188,241,194,241,198,242,202,243,208,241,215,242,218,216,219" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Arm String"  data-map="fortythree" shape="poly" coords="182,217,208,217,209,254,208,258,189,257,184,247,183,234,182,228" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Arm String"  data-map="fortyfour" shape="poly" coords="213,218,242,218,241,230,241,243,236,256,217,256" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Knee Back"  data-map="forty-five" shape="poly" coords="189,257,207,257,206,268,206,276,208,282,189,282,191,274,192,265" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Knee Back"  data-map="fortysix" shape="poly" coords="215,257,237,257,233,263,232,270,232,277,234,282,218,281,218,275,219,264" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Calf"  shape="poly" data-map="fortyseven" coords="189,282,207,282,208,288,209,295,209,304,207,312,206,325,195,326,193,316,189,301,188,293,187,286" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Calf"  shape="poly"  data-map="fortyeight" coords="217,282,235,282,237,291,235,301,232,313,228,326,218,325,215,304,215,295" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Left Ankle"  shape="poly"  data-map="fortynine" coords="195,324,206,323,207,336,198,336,196,335" />
                                                <area alt="" data-parentid = "0" data-isparent="0"  onclick="changeImage()" title="Right Ankle"  shape="poly" data-map="fifty" coords="195,324,206,323,207,336,198,336,196,335" />
                                                </map>
                                                </div></script>
                                                        <!--male total parts -->

                                                        <!--female total parts -->
                                                        <script type="text/template" id="tmp-female">
                                                                <div class="img-wrap female" >
                                                <div class="canvas">
                                                <canvas id='image1_canvas'></canvas>
                                                <canvas id='image1_canvas_marked'></canvas>
                                                </div>
                                                <img src="{{ admin_url('public/assets/images/human_body_parts/female/full.png') }}"  usemap='#imgmap_1' class='imgmap_1' title='imgmap1' alt='imgmap1' id='img-imgmap1' />
                                                <map id='imgmap1' name='imgmap_1' data-type='total' data-map="total">
                                                <area alt="" onclick="changeImage('.female-face')" title="Head"  data-map="one" shape="poly" coords="56,43,55,32,59,25,61,23,66,21,74,21,80,23,86,30,87,34,88,41,87,45,88,47,86,53,85,55,82,60,78,64,74,68,69,69,68,70,61,65,59,57,56,54,54,50,53,46" />
                                                <area alt="" onclick="changeImage()" title="Neck"  data-map="two" shape="poly" coords="48,81,87,82,82,72,82,58,71,69,72,70,59,62,57,57,58,67,59,74,56,77" />
                                                <area alt="" onclick="changeImage()" title="Left Shoulder Front" data-map="three"  shape="poly" coords="69,83,87,81,103,86,110,92,113,103,83,104,69,102,70,102" />
                                                <area alt="" onclick="changeImage()" title="Right Shoulder Front" data-map="four"  shape="poly" coords="70,103,23,104,25,94,30,87,36,85,47,82,59,82,69,83" />
                                                <area alt="" onclick="changeImage()" title="Left Arm Front"  data-map="five" shape="poly" coords="99,103,113,104,114,113,119,140,103,149,100,130" />
                                                <area alt="" onclick="changeImage()" title="Right Arm Front"  data-map="six" shape="poly" coords="25,104,25,124,23,142,39,147,42,129,41,107,40,103" />
                                                <area alt="" onclick="changeImage()" title="Left Elbow Front"  data-map="seven" shape="poly" coords="106,163,123,157,120,142,102,150" />
                                                <area alt="" onclick="changeImage()" title="Right Elbow Front"  data-map="eight" shape="poly" coords="39,148,37,162,21,155,23,142" />
                                                <area alt="" onclick="changeImage()" title="Left Forearm Front"  data-map="nine" shape="poly" coords="107,164,123,157,125,170,125,182,125,189,127,195,127,195,118,199" />
                                                <area alt="" onclick="changeImage()" title="Right Forearm Front"  data-map="ten" shape="poly" coords="20,156,36,161,26,197,18,196,20,181" />
                                                <area alt="" onclick="changeImage('.female-hand-right')" title="Right Hand"  shape="poly" data-map="eleven" coords="18,195,25,196,26,197,27,212,26,220,23,220,25,215,23,223,22,223,21,225,22,215,21,218,18,226,17,224,17,216,15,220,13,222,13,221,16,207,15,207,13,205,12,206,9,207,7,207,7,206,7,206,6,206" />
                                                <area alt="" onclick="changeImage('.female-hand-left')" title="Left Hand"  shape="poly" data-map="twelve" coords="126,193,118,199,118,205,118,213,118,221,122,222,122,215,121,215,124,225,125,218,128,226,130,224,130,215,129,214,134,224,131,209,133,206,137,209,140,209,140,209,140,208" />
                                                <area alt="" onclick="changeImage()" title="Left Brest"  shape="poly" data-map="thirteen" coords="69,104,88,103,99,104,100,118,98,132,72,133,69,132" />
                                                <area alt="" onclick="changeImage()" title="Right Brest"  shape="poly" data-map="fourteen" coords="41,103,62,103,69,104,70,127,70,131,44,133" />
                                                <area alt="" onclick="changeImage()" title="Stomach"  shape="poly" data-map="fifteen" coords="41,134,70,133,98,133,97,145,97,155,97,158,71,159,48,158" />
                                                <area alt="" onclick="changeImage()" title="Left Hip"  shape="poly" data-map="sixteen" coords="73,176,107,175,103,164,99,158,77,158,73,158" />
                                                <area alt="" onclick="changeImage()" title="Right Hip"  shape="poly" data-map="seventeen" coords="47,159,72,159,71,175,50,175,38,175,41,165" />
                                                <area alt="" onclick="changeImage()" title="Pubis"  shape="poly" data-map="eighteen" coords="38,175,62,176,86,176,105,176,106,176,108,192,108,213,107,222,77,222,46,222,37,221,34,202" />
                                                <area alt="" onclick="changeImage()" title="Left Thigh"  shape="poly" data-map="ninteen" coords="70,224,106,223,96,270,72,266,76,242" />
                                                <area alt="" onclick="changeImage()" title="Right Thigh"  shape="poly" data-map="twenty" coords="38,224,71,223,69,266,46,269" />
                                                <area alt="" onclick="changeImage()" title="Left Knee"  shape="poly" data-map="twentyone" coords="72,267,97,272,94,287,74,284" />
                                                <area alt="" onclick="changeImage()" title="Right Knee"  shape="poly" data-map="twentytwo"coords="46,269,70,267,69,285,47,289" />
                                                <area alt="" onclick="changeImage()" title="Left Leg"  shape="poly" data-map="twentythree" coords="72,284,93,286,98,304,94,321,84,348,73,352" />
                                                <area alt="" onclick="changeImage()" title="Right Leg"  shape="poly" data-map="twentyfour" coords="69,284,70,309,68,329,67,339,70,354,58,351,53,328,48,308,46,294,46,288" />
                                                <area alt="" onclick="changeImage()" title="Left Ankle"  shape="poly" data-map="twentyfive" coords="74,352,84,350,84,361,72,362" />
                                                <area alt="" onclick="changeImage()" title="Right Ankle"  shape="poly" data-map="twentysix" coords="59,353,69,356,71,366,57,364" />
                                                <area alt="" onclick="changeImage('.female-foot-left')" title="Left Foot"  shape="poly" data-map="twentyseven" coords="72,363,79,361,84,360,89,365,94,373,93,373,91,372,91,374,92,376,89,375,87,377,83,372,84,376,82,378" />
                                                <area alt="" onclick="changeImage('.female-foot-right')" title="Right Foot"  shape="poly" data-map="twentyeight" coords="53,371,57,363,64,364,68,366,70,367,71,372,69,377,65,379,64,375,63,377,61,376,61,376,60,377,60,375,58,377,57,375,55,376,55,374" />
                                                <area alt="" onclick="changeImage()" title="Back Skull"  shape="poly" data-map="twentynine" coords="210,62,225,57,228,50,228,44,226,44,226,34,223,27,219,23,214,21,207,21,200,23,196,29,194,40,194,44,194,46,195,52,199,56,199,61,201,58" />
                                                <area alt="" onclick="changeImage()" title="Neck Back"  shape="poly" data-map="thirty" coords="198,57,209,62,222,59,222,65,222,72,223,75,218,75,212,75,205,75,198,75,200,68" />
                                                <area alt="" onclick="changeImage()" title="Left Shoulder Back"  shape="poly" data-map="thirtyone" coords="169,99,188,99,207,99,209,100,210,89,210,76,198,75,186,82,177,85,171,91" />
                                                <area alt="" onclick="changeImage()" title="Right Shoulder Back"  shape="poly" data-map="thirtytwo" coords="211,76,222,76,226,77,232,80,240,82,246,83,254,88,256,96,254,98,242,98,229,98,217,98,210,98" />
                                                <area alt="" onclick="changeImage()" title="Left Arm Back"  shape="poly" data-map="thirtythree" coords="180,111,175,98,169,98,167,110,167,124,166,135,172,139,179,143,181,144,183,126" />
                                                <area alt="" onclick="changeImage()" title="Right Arm Back"  shape="poly" data-map="thirtyfour" coords="242,98,247,98,255,98,256,108,257,125,258,142,250,146,244,146,241,121,244,108" />
                                                <area alt="" onclick="changeImage()" title="Left Elbow Back"  shape="poly" data-map="thirtyfive" coords="168,136,180,143,180,152,177,163,175,166,171,164,166,160,163,158" />
                                                <area alt="" onclick="changeImage()" title="Right Elbow Back"  shape="poly" data-map="thirtysix" coords="243,148,252,147,257,145,260,153,261,161,256,165,248,168" />
                                                <area alt="" onclick="changeImage()" title="Left Forearm Back"  shape="poly" data-map="thirtyseven" coords="164,157,175,167,168,199,161,194,161,174,161,162,161,158" />
                                                <area alt="" onclick="changeImage()" title="Right Forearm Back"  shape="poly" data-map="thirtyeight" coords="248,169,261,161,265,174,263,186,263,197,253,202" />
                                                <area alt="" onclick="changeImage()" title="Upper Back"  shape="poly" data-map="thirtynine" coords="177,99,179,108,183,121,184,127,187,145,200,145,210,145,222,144,230,145,237,145,240,131,241,122,241,113,242,101,241,99" />
                                                <area alt="" onclick="changeImage()" title="Lower Back"  shape="poly" data-map="fourty" coords="186,144,238,144,237,150,235,156,237,159,239,163,242,167,244,171,246,177,237,177,178,177,188,154" />
                                                <area alt="" onclick="changeImage()" title="Left Buttock "  shape="poly" data-map="fourtyone" coords="176,179,209,179,209,216,173,217,172,199" />
                                                <area alt="" onclick="changeImage()" title="Right Buttock "  shape="poly" data-map="fourtytwo" coords="209,178,246,178,248,189,249,199,249,205,248,216,235,217,220,217,209,216" />
                                                <area alt="" onclick="changeImage()" title="Left Hamstring"  shape="poly" data-map="fourtythree" coords="207,215,174,216,175,228,177,236,179,242,182,251,184,255,184,261,199,262,208,261,209,243,207,259,209,237" />
                                                <area alt="" onclick="changeImage()" title="Right Hamstring"  shape="poly" data-map="fourtyfour" coords="209,216,209,243,212,252,211,262,230,262,239,263,242,253,246,242,247,229,248,216" />
                                                <area alt="" onclick="changeImage()" title="Left Knee Back"  shape="poly" data-map="fourtyfive" coords="184,262,206,262,208,269,208,279,208,284,207,288,207,290,197,290,184,290" />
                                                <area alt="" onclick="changeImage()" title="Right Knee Back"  shape="poly" data-map="fourtysix" coords="212,264,212,262,239,262,236,272,235,277,235,282,235,287,237,290,228,290,221,290,216,289,213,289,211,282,212,274" />
                                                <area alt="" onclick="changeImage()" title="Left Calf"  shape="poly" data-map="fourtyseven" coords="185,291,206,291,208,301,210,307,210,321,209,333,207,340,193,340,186,323,183,304" />
                                                <area alt="" onclick="changeImage()" title="Right Calf"  shape="poly" data-map="fourtyeight" coords="214,291,232,291,234,292,236,300,237,305,236,311,234,321,232,332,229,339,221,339,214,339,210,320" />
                                                <area alt="" onclick="changeImage()" title="Left Ankle"  shape="poly" data-map="fourtynine" coords="193,340,208,338,208,348,208,354,209,360,210,365,208,368,198,356" />
                                                <area alt="" onclick="changeImage()" title="Right Ankle"  shape="poly" data-map="fifty" coords="213,367,226,359,227,352,229,345,231,340,223,339,219,339,215,341,215,339" />
                                                <area alt="" onclick="changeImage()" title="Left Toe"  shape="poly" data-map="fiftyone" coords="192,365,194,365,197,363,198,360,199,358,203,363,208,367,210,373,208,377,204,380,196,369" />
                                                <area alt="" onclick="changeImage()" title="Right Toe"  shape="poly" data-map="fiftytwo" coords="214,366,223,359,226,363,230,364,231,366,231,369,226,373,222,376,218,377,214,376,212,371" />
                                                </map>
                                                </div></script>
                                                        <!--female total parts -->

                                                    </div>
                                                    <div class="img-sgl-content col-lg-4 col-md-4 col-sm-4"
                                                        data-type='img-full'>

                                                        <div class="image-container">
                                                            <div class="img-map-parts">
                                                                <!--male otheer parts -->
                                                                <div class="parts male-foot-right">
                                                                    <h4>Foot Right</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/male/front/Right-Foot.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="foot-right"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right Finger 1"
                                                                            data-map='foot-right-finger1'
                                                                            shape="poly"
                                                                            coords="599,779,604,796,591,823,586,844,592,868,605,883,616,890,631,897,648,898,667,895,678,886,692,878,701,861,701,845,700,814,701,789,702,764,705,747,703,737" />
                                                                        <area alt="" title="B. Right Finger 2"
                                                                            data-map='foot-right-finger2'
                                                                            shape="poly"
                                                                            coords="506,779,503,808,496,835,487,868,486,885,493,898,505,904,525,902,549,892,565,860,568,845,573,818,590,784,593,779" />
                                                                        <area alt="" title="C. Right Finger 3"
                                                                            data-map='foot-right-finger3'
                                                                            shape="poly"
                                                                            coords="444,757,430,793,415,832,410,859,417,874,444,877,466,867,478,829,491,796,502,779" />
                                                                        <area alt="" title="D. Right Finger 4"
                                                                            data-map='foot-right-finger4'
                                                                            shape="poly"
                                                                            coords="403,731,416,740,440,756,434,776,426,794,416,812,407,835,390,846,374,843,362,833,359,816,367,792" />
                                                                        <area alt="" title="E. Right Finger 5"
                                                                            data-map='foot-right-finger5'
                                                                            shape="poly"
                                                                            coords="373,687,404,730,378,765,361,780,344,777,333,756,339,732" />
                                                                    </map>
                                                                </div>

                                                                <div class="parts male-foot-left">
                                                                    <h4>Foot Left</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/male/front/left-foot-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="foot-left"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Left Finger 1"
                                                                            data-map='foot-left-finger1'
                                                                            shape="poly"
                                                                            coords="336,737,442,777,438,787,441,796,447,810,456,835,447,868,432,887,396,897,369,892,358,881,347,866,343,850" />
                                                                        <area alt="" title="B. Left Finger 2"
                                                                            data-map='foot-left-finger2'
                                                                            shape="poly"
                                                                            coords="443,780,459,788,466,813,471,834,478,861,488,887,501,896,526,903,540,903,549,894,558,877,554,857,545,831,541,811,537,799,537,777" />
                                                                        <area alt="" title="C. Left Finger 3"
                                                                            data-map='foot-left-finger3'
                                                                            shape="poly"
                                                                            coords="540,779,598,756,618,798,627,835,637,861,622,874,596,876,582,872,574,868,553,801" />
                                                                        <area alt="" title="D. Left Finger 4"
                                                                            data-map='foot-left-finger4'
                                                                            shape="poly"
                                                                            coords="598,760,636,730,660,766,674,788,684,808,683,821,680,836,666,844,645,847,632,830" />
                                                                        <area alt="" title="E. Left Finger 5"
                                                                            data-map='foot-left-finger5'
                                                                            shape="poly"
                                                                            coords="642,730,667,686,689,718,704,739,708,759,703,776,688,779,674,779,657,756" />
                                                                    </map>
                                                                </div>

                                                                <div class="parts male-face">
                                                                    <h4>Head</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/male/front/male-face.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="head"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right skull"
                                                                            shape="poly" data-map="A"
                                                                            coords="408,120,306,121,279,119,258,120,249,121,250,78,249,25,282,26,302,32,323,37,352,49,375,66,394,84,403,106" />
                                                                        <area alt="" title="B. Left Skull"
                                                                            shape="poly" data-map="B"
                                                                            coords="250,29,251,118,88,121,95,106,105,89,113,76,127,63,137,54,154,46,171,38,194,32,215,27,243,26" />
                                                                        <area alt="" title="C. Right Forehead"
                                                                            shape="poly" data-map="C"
                                                                            coords="250,121,406,122,417,151,417,172,415,198,409,206,310,206,250,208" />
                                                                        <area alt="" title="D. Left Forehead"
                                                                            shape="poly" data-map="D"
                                                                            coords="250,121,250,208,89,207,82,176,82,156,88,130,89,121" />
                                                                        <area alt="" title="E. Bridge of Nose"
                                                                            shape="poly" data-map="E"
                                                                            coords="250,239,210,309,287,310" />
                                                                        <area alt="" title="F. Left Eyebrow"
                                                                            shape="poly" data-map="F"
                                                                            coords="102,243,248,242,250,206,127,207,89,207,90,214,97,217,103,225" />
                                                                        <area alt="" title="G. Right Eyebrow"
                                                                            shape="poly" data-map="G"
                                                                            coords="386,243,250,242,250,206,410,206,406,218,398,233,390,243" />
                                                                        <area alt="" id="test"
                                                                            title="H. Left Eye" shape="poly"
                                                                            data-map="H"
                                                                            coords="216,297,102,297,104,244,246,244" />
                                                                        <area alt="" title="I. Right Eye"
                                                                            shape="poly" data-map="I"
                                                                            coords="250,245,278,296,385,295,384,245" />
                                                                        <area alt="" title="J. Nose"
                                                                            shape="poly" data-map="J"
                                                                            coords="210,312,286,311,322,381,173,382" />
                                                                        <area alt="" title="K. Mouth"
                                                                            shape="poly" data-map="K"
                                                                            coords="173,382,322,381,338,411,158,413" />
                                                                        <area alt="" title="L. Left Cheeks"
                                                                            shape="poly" data-map="L"
                                                                            coords="145,435,217,296,104,296,102,330,108,352,113,365,115,380,115,398,118,412,134,429" />
                                                                        <area alt="" title="M. Right Cheeks"
                                                                            shape="poly" data-map="M"
                                                                            coords="349,429,311,362,280,297,385,297,386,335,381,354,381,366,373,387,368,400,362,412" />
                                                                        <area alt="" title="N. Left Ear"
                                                                            shape="poly" data-map="N"
                                                                            coords="103,225,104,333,96,331,86,327,77,314,70,289,67,269,66,246,70,230,80,216,86,211,101,222" />
                                                                        <area alt="" title="O. Right Ear"
                                                                            shape="poly" data-map="O"
                                                                            coords="385,245,385,334,404,326,414,314,419,298,428,282,433,266,434,255,431,234,422,221,407,212,399,230,393,238,388,244" />
                                                                        <area alt="" title="P. Left Jaw"
                                                                            shape="poly" data-map="P"
                                                                            coords="154,443,249,442,250,410,159,411,151,421,144,434,151,440" />
                                                                        <area alt="" title="Q. Right Jaw"
                                                                            shape="poly" data-map="Q"
                                                                            coords="330,444,248,444,250,411,338,411,352,427,341,437,334,442" />
                                                                        <area alt="" title="R. Chin"
                                                                            shape="poly" data-map="R"
                                                                            coords="159,443,249,442,333,442,318,454,302,466,287,471,258,474,237,474,205,474,196,472,177,462,164,451,158,446" />
                                                                    </map>
                                                                </div>

                                                                <div class="parts male-hand-right">
                                                                    <h4>Right Hand</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/male/front/male-right-hand.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="hand-right"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right Palm"
                                                                            data-map='hand-right-palm' shape="poly"
                                                                            coords="383,283,384,269,384,256,384,250,382,235,382,221,379,209,376,197,374,187,371,178,368,170,365,164,364,160,243,135,231,140,225,144,218,149,212,155,205,160,198,169,196,171,191,230,201,236,206,243,212,255,212,268,210,278,207,289,207,298,196,312,194,314,240,322,253,325,275,325,293,324,332,312,347,307,336,308,347,306" />
                                                                        <area alt=""
                                                                            title="B. Right Thumb Finger"
                                                                            data-map='hand-right-thumb' shape="poly"
                                                                            coords="136,203,145,199,153,195,161,193,169,189,171,189,175,185,179,180,184,179,186,176,191,173,197,171,191,233,183,236,173,239,164,242,157,245,151,249,138,252,129,252,117,253,105,255,97,254,91,250,87,244,83,238,83,230,90,222,102,216,112,211,120,208,124,206" />
                                                                        <area alt=""
                                                                            title="C. Right Index Finger"
                                                                            data-map='hand-right-index' shape="poly"
                                                                            coords="197,426,195,434,192,439,188,442,183,444,178,445,168,443,162,437,159,430,159,426,159,421,161,412,162,404,165,396,167,389,170,379,172,373,176,358,182,347,183,343,187,331,189,325,194,316,194,315,241,323,218,375,207,397,205,405,203,405" />
                                                                        <area alt=""
                                                                            title="D. Right Middle Finger"
                                                                            data-map='hand-right-middle'
                                                                            shape="poly"
                                                                            coords="249,324,293,325,289,333,287,342,288,351,289,361,285,379,281,389,279,394,280,403,278,416,278,426,278,437,278,452,278,464,276,473,271,479,264,480,258,480,251,480,243,473,240,465,237,447,237,423,237,408,236,390,240,376,241,357" />
                                                                        <area alt=""
                                                                            title="E. Right Ring Finger"
                                                                            data-map='hand-right-ring' shape="poly"
                                                                            coords="295,324,331,313,334,327,334,339,334,351,335,359,334,366,333,371,333,381,333,391,331,400,331,407,331,413,330,423,329,429,327,436,325,444,322,450,315,451,305,453,296,447,291,439,291,410,294,404,294,393,293,377,291,362,291,352,291,347" />
                                                                        <area alt=""
                                                                            title="F. Right Little Finger"
                                                                            data-map='hand-right-little'
                                                                            shape="poly"
                                                                            coords="347,306,382,284,394,306,399,321,406,340,411,352,412,359,415,370,416,384,415,392,408,397,397,397,385,393,377,368,369,354,366,339,359,334" />

                                                                    </map>
                                                                </div>

                                                                <div class="parts male-hand-left">
                                                                    <h4>Left Hand</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/male/front/malelefthand-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="hand-left"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Left Palm"
                                                                            data-map='hand-left-palm' shape="poly"
                                                                            coords="130,158,258,132,269,135,277,141,284,146,291,152,297,158,304,166,305,168,312,232,301,234,297,240,291,252,290,257,293,280,296,298,309,317,260,326,251,326,205,327,165,314,150,308,112,285,110,260,112,231,115,209" />
                                                                        <area alt=""
                                                                            title="B. Left Thumb Finger"
                                                                            data-map='hand-left-thumb' shape="poly"
                                                                            coords="311,233,304,168,318,174,327,181,331,186,346,191,361,197,367,201,380,205,392,210,405,216,413,219,419,224,422,228,423,235,421,241,415,249,411,253,397,255,371,253,345,243" />
                                                                        <area alt=""
                                                                            title="C. Left Index Finger"
                                                                            data-map='hand-left-index' shape="poly"
                                                                            coords="261,325,309,318,317,333,319,343,321,348,324,353,328,364,337,393,339,404,343,416,345,430,343,440,340,446,333,450,319,452,309,444,307,434,299,413,293,399,279,368,274,362,261,330" />
                                                                        <area alt=""
                                                                            title="D. Left Middle Finger"
                                                                            data-map='hand-left-middle' shape="poly"
                                                                            coords="252,325,256,342,257,351,259,361,261,372,261,381,264,391,264,400,264,408,264,418,264,426,264,438,262,454,262,465,257,480,249,488,235,490,225,483,221,474,220,454,220,432,219,416,219,401,220,397,216,390,213,382,211,368,211,358,211,349,211,342,210,334,208,328" />
                                                                        <area alt=""
                                                                            title="E. Left Ring Finger"
                                                                            data-map='hand-left-ring' shape="poly"
                                                                            coords="203,327,205,336,207,343,208,354,208,364,206,372,206,385,206,394,205,403,205,411,206,422,207,434,207,444,206,452,203,455,199,458,193,460,187,461,181,461,174,457,171,450,169,438,169,431,166,425,166,416,166,408,163,396,164,387,165,379,163,371,164,367,163,364,162,356,162,349,163,339,163,331,164,320,167,315" />
                                                                        <area alt=""
                                                                            title="F. Left Little Finger"
                                                                            data-map='hand-left-little' shape="poly"
                                                                            coords="113,284,151,309,143,322,137,336,132,342,129,345,129,351,126,360,120,369,118,378,114,386,109,396,105,401,98,404,91,404,82,402,78,395,76,385,82,358,95,323" />

                                                                    </map>
                                                                </div>
                                                                <!--male other parts -->


                                                                <!--Female parts-->

                                                                <div class="parts female-foot-right">
                                                                    <h4>Foot Right</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/female/front/rightfoot-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="foot-right"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right Finger 1"
                                                                            data-map='foot-right-finger1'
                                                                            shape="poly"
                                                                            coords="573,764,629,760,626,781,625,796,627,802,629,836,629,857,621,885,603,903,590,906,566,896,552,884,547,855,552,826" />
                                                                        <area alt="" title="B. Right Finger 2"
                                                                            data-map='foot-right-finger2'
                                                                            shape="poly"
                                                                            coords="514,778,569,763,555,817,546,844,536,874,523,885,508,886,497,879,496,852" />
                                                                        <area alt="" title="C. Right Finger 3"
                                                                            data-map='foot-right-finger3'
                                                                            shape="poly"
                                                                            coords="507,796,508,775,494,767,475,758,469,788,461,814,458,843,462,857,475,860,487,856,498,843" />
                                                                        <area alt="" title="D. Right Finger 4"
                                                                            data-map='foot-right-finger4'
                                                                            shape="poly"
                                                                            coords="439,703,458,732,472,754,469,775,462,810,453,829,436,829,427,816,422,797" />
                                                                        <area alt="" title="E. Right Finger 5"
                                                                            data-map='foot-right-finger5'
                                                                            shape="poly"
                                                                            coords="430,697,431,744,424,773,420,781,406,781,400,763,399,708,397,674,399,674" />
                                                                    </map>
                                                                </div>
                                                                <div class="parts female-foot-left">
                                                                    <h4>Foot Left</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/female/front/left-foot-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="foot-left"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Left Finger 1"
                                                                            data-map='foot-left-finger1'
                                                                            shape="poly"
                                                                            coords="413,761,468,764,483,801,495,833,499,863,489,883,481,897,462,904,444,907,431,898,422,883,411,850,415,790" />
                                                                        <area alt="" title="B. Left Finger 2"
                                                                            data-map='foot-left-finger2'
                                                                            shape="poly"
                                                                            coords="469,765,523,777,527,795,533,808,540,830,543,845,545,851,546,858,546,870,543,885,529,887,517,885,503,869,488,820" />
                                                                        <area alt="" title="C. Left Finger 3"
                                                                            data-map='foot-left-finger3'
                                                                            shape="poly"
                                                                            coords="532,777,534,799,538,825,546,853,561,862,571,858,582,849,582,823,580,797,567,756" />
                                                                        <area alt="" title="D. Left Finger 4"
                                                                            data-map='foot-left-finger4'
                                                                            shape="poly"
                                                                            coords="569,754,599,706,613,770,617,802,610,823,600,829,583,828" />
                                                                        <area alt="" title="E. Left Finger 5"
                                                                            data-map='foot-left-finger5'
                                                                            shape="poly"
                                                                            coords="611,701,612,744,619,780,637,777,643,763,644,720,645,669" />
                                                                    </map>
                                                                </div>
                                                                <div class="parts female-hand-right">
                                                                    <h4>Right Hand</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/female/front/female-right-hand-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="hand-right"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right Palm"
                                                                            data-map='hand-right-palm' shape="poly"
                                                                            coords="348,136,348,92,349,29,347,21,291,14,231,18,235,52,232,91,225,116,210,141,189,160,175,175,196,232,203,239,214,250,222,275,226,299,224,317,249,319,285,325,308,326,351,305,374,292,370,228,362,174" />
                                                                        <area alt=""
                                                                            title="B. Right Thumb Finger"
                                                                            data-map='hand-right-thumb' shape="poly"
                                                                            coords="177,175,193,231,181,243,170,254,155,273,127,287,111,285,107,277,124,260" />
                                                                        <area alt=""
                                                                            title="C. Right Index Finger"
                                                                            data-map='hand-right-index' shape="poly"
                                                                            coords="246,456,251,419,257,379,256,348,260,324,224,318,220,380,221,425,221,439,219,453,233,461" />
                                                                        <area alt=""
                                                                            title="D. Right Middle Finger"
                                                                            data-map='hand-right-middle'
                                                                            shape="poly"
                                                                            coords="264,322,272,412,271,433,272,456,272,473,277,482,284,485,295,482,300,471,303,439,303,363,303,331,305,325" />
                                                                        <area alt=""
                                                                            title="E. Right Ring Finger"
                                                                            data-map='hand-right-ring' shape="poly"
                                                                            coords="308,324,344,310,347,345,348,395,348,433,346,453,342,461,330,463,320,449,321,410" />
                                                                        <area alt=""
                                                                            title="F. Right Little Finger"
                                                                            data-map='hand-right-little'
                                                                            shape="poly"
                                                                            coords="364,365,370,395,381,404,389,406,392,395,394,377,392,363,388,341,384,315,376,289,347,308" />

                                                                    </map>
                                                                </div>
                                                                <div class="parts female-hand-left">
                                                                    <h4>Left Hand</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/female/front/female-left-hand-01.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="hand-left"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Left Palm"
                                                                            data-map='hand-left-palm' shape="poly"
                                                                            coords="152,20,177,14,209,13,230,13,250,16,268,18,267,36,266,54,267,68,267,79,269,94,274,106,278,120,284,133,294,144,305,152,314,162,322,173,316,198,309,221,304,235,291,243,283,258,280,272,277,286,276,295,277,315,267,318,239,326,221,326,201,326,194,325,173,317,159,310,148,302,124,289,126,262,128,242,132,210,138,187,142,158,146,151,150,134,153,106" />
                                                                        <area alt=""
                                                                            title="B. Left Thumb Finger"
                                                                            data-map='hand-left-thumb' shape="poly"
                                                                            coords="364,237,372,253,378,261,388,269,394,275,391,283,384,287,366,283,350,275,335,263,319,243,312,236,304,234,311,211,320,185,325,174" />
                                                                        <area alt=""
                                                                            title="C. Left Index Finger"
                                                                            data-map='hand-left-index' shape="poly"
                                                                            coords="269,461,277,455,280,450,282,421,279,364,275,316,262,320,249,323,240,324,243,360,245,398,251,440,258,459" />
                                                                        <area alt=""
                                                                            title="D. Left Middle Finger"
                                                                            data-map='hand-left-middle' shape="poly"
                                                                            coords="193,325,214,326,236,324,235,327,234,340,233,361,232,388,231,409,228,422,228,448,226,478,218,488,204,484,197,461,198,335" />
                                                                        <area alt=""
                                                                            title="E. Left Ring Finger"
                                                                            data-map='hand-left-ring' shape="poly"
                                                                            coords="155,308,172,317,189,324,190,339,186,371,181,400,180,423,176,451,171,462,164,463,153,456,151,422,151,374" />
                                                                        <area alt=""
                                                                            title="F. Left Little Finger"
                                                                            data-map='hand-left-little' shape="poly"
                                                                            coords="105,389,106,398,111,407,120,406,123,401,130,392,133,379,138,356,143,339,146,324,149,317,151,307,144,302,137,295,125,289,117,312" />

                                                                    </map>
                                                                </div>
                                                                <div class="parts female-face">
                                                                    <h4>Head</h4>
                                                                    <img src="{{ admin_url('public/assets/images/human_body_parts/female/front/female-face.png') }}"
                                                                        usemap='#imgmap_css_container_imgmap201293016112'
                                                                        class='imgmap_css_container'
                                                                        title='imgmap201293016112'
                                                                        alt='imgmap201293016112'
                                                                        id='img-imgmap201293016112' />
                                                                    <map id='imgmap201293016112' data-map="head"
                                                                        name='imgmap_css_container_imgmap201293016112'>
                                                                        <area alt="" title="A. Right skull"
                                                                            data-map='A' shape="poly"
                                                                            coords="250,16,250,82,124,82,130,67,137,60,149,49,159,41,171,36,177,29,193,24,216,18" />

                                                                        <area alt="" title="B. Left Skull"
                                                                            data-map='B' shape="poly"
                                                                            coords="379,82,249,83,249,16,264,16,284,20,301,24,313,29,325,33,342,40,354,51,365,61,376,73" />

                                                                        <area alt="" title="C. Right Forehead"
                                                                            data-map='C' shape="poly"
                                                                            coords="107,134,249,132,250,83,121,82,115,97,109,112,105,124" />

                                                                        <area alt="" title="D. Left Forehead"
                                                                            data-map='D' shape="poly"
                                                                            coords="397,133,250,133,251,82,380,81,387,91,393,111,395,123" />

                                                                        <area alt="" title="E. Bridge of Nose"
                                                                            data-map='E' shape="poly"
                                                                            coords="250,191,198,306,306,305" />

                                                                        <area alt="" title="F. Left Eyebrow"
                                                                            data-map='F' shape="poly"
                                                                            coords="250,194,395,193,396,181,398,162,398,146,397,133,250,133" />

                                                                        <area alt="" title="G. Right Eyebrow"
                                                                            data-map='G' shape="poly"
                                                                            coords="105,193,249,194,250,134,106,135,104,152,105,163,106,174,106,187" />

                                                                        <area alt="" id="test"
                                                                            title="H. Left Eye" data-map='H'
                                                                            shape="poly"
                                                                            coords="250,193,295,283,398,283,401,274,402,252,400,231,399,213,396,192" />

                                                                        <area alt="" title="I. Right Eye"
                                                                            data-map='I' shape="poly"
                                                                            coords="102,285,209,282,251,195,250,192,104,192,104,203,102,218,101,229,99,240,99,254,102,276" />

                                                                        <area alt="" title="J. Nose"
                                                                            data-map='J' shape="poly"
                                                                            coords="199,305,304,306,326,351,175,351" />

                                                                        <area alt="" title="K. Mouth"
                                                                            data-map='K' shape="poly"
                                                                            coords="174,353,174,429,327,427,327,354" />

                                                                        <area alt="" title="L. Left Cheeks"
                                                                            data-map='L' shape="poly"
                                                                            coords="397,286,294,285,326,353,327,382,326,430,347,431,358,416,372,396,380,377,383,359,387,341" />

                                                                        <area alt="" title="M. Right Cheeks"
                                                                            data-map='M' shape="poly"
                                                                            coords="102,285,210,283,175,352,174,429,156,429,146,419,137,407,126,390,122,372,118,356,118,338,112,320" />

                                                                        <area alt="" title="N. Left Ear"
                                                                            data-map='N' shape="poly"
                                                                            coords="398,208,400,223,402,237,402,255,400,274,395,295,393,310,393,313,398,317,405,320,414,321,418,313,419,306,420,297,421,287,420,276,420,265,424,253,424,243,426,235,428,220,426,207,422,201,418,196,413,195,405,199,399,205" />

                                                                        <area alt="" title="O. Right Ear"
                                                                            data-map='O' shape="poly"
                                                                            coords="106,206,100,235,99,250,101,263,102,277,103,288,107,298,109,308,112,314,104,316,95,322,87,320,83,314,78,305,79,293,81,282,80,273,79,266,79,258,78,251,76,245,73,235,72,225,74,214,75,206,78,198,86,194,94,198" />

                                                                        <area alt="" title="P. Left Jaw"
                                                                            data-map='P' shape="poly"
                                                                            coords="320,452,252,450,254,427,347,430,334,441,327,450" />

                                                                        <area alt="" title="Q. Right Jaw"
                                                                            data-map='Q' shape="poly"
                                                                            coords="182,452,254,453,254,430,158,429,161,439,174,447,181,452" />

                                                                        <area alt="" title="R. Chin"
                                                                            data-map='R' shape="poly"
                                                                            coords="181,451,320,452,302,465,290,475,277,483,252,480,240,483,222,479,200,469" />

                                                                    </map>
                                                                </div>

                                                                <!--/Female other parts-->
                                                            </div>
                                                        </div>
                                                        <canvas id='image2_canvas'></canvas><!-- green-->
                                                        <canvas id='image2_canvas_marked'></canvas><!-- red-->
                                                    </div>
                                                    @if ($is_ready_only != 1)
                                                        <div class="img-desc_new">
                                                            <b>{{ 'Description' }}:
                                                                <span class="float-right">
                                                                    <div class="btn btn-warning btn-sm addbodyparts">
                                                                        {{ 'Add' }}</div>
                                                                </span>
                                                            </b> <label id='des_injury1_label'></label>

                                                        </div>
                                                    @endif
                                                    <div class="img-desc col-lg-4 col-md-4 col-sm-4"
                                                        style="float: right;">
                                                    </div>


                                                </div>

                                            </div>
                                        </div>


                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <input type="hidden" name="injury_person_type" id="injury_person_type"
                                    value="{{ $injury_person_type }}">
                                <input type="hidden" name="injuredPerson" id="injuredPerson"
                                    value="{{ $injured_person_id }}">

                                @if ($is_ready_only != 1)
                                    <div class="savesubmit text-center">
                                        <div class="">
                                            <button name="save_inj"
                                                style="background-color: #086ca6 !important;border-color: #086ca6 !important;"
                                                type="submit" id="button" value="Save & Submit"
                                                class="btn btn-secondary save_inj">{{ 'Save' }}</button>

                                            <button type="button"
                                                style="background-color: #fd3550;border-color: #fd3550;"
                                                class="btn btn-secondary btn-warnings injcancel center"
                                                data-bs-dismiss="modal">{{ 'Cancel' }}</button>

                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- invetigation form end-->
                    </form>
                </div>
                <!--<div class="modal-footer">
                                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                               </div>-->
            </div>
        </div>
    </div>

    <script src="{{ public_plugins('jquery/jquery.min.js') }}"></script>
    <script src="{{ public_plugins('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/jquery.validate.min.js') }}"></script>
    {{-- <script src="{{ public_plugins('datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script> --}}
    <script src="{{ public_plugins('sweetalert/SweetAlertFull.js') }}"></script>

    <script src="{{ url('public/assets/js/rwdImageMaps.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // statusCode: {
                //     419: function() {
                //         window.location.href = '{{ url('') }}';
                //     }
                // }
            });
            $('#humanBody').rwdImageMaps();


        });


        $("#injury_model").on("shown.bs.modal", function() {

            $('.injury-box').removeClass('hide');

        });

        $(document).on('click', '.injcancel', function() {

            $("#injury_model").modal("hide");
        });

        $(document).on('click', '.getbody', function() {
            var injurydetails = $(this).attr('id');
            var res = injurydetails.split('_');
            var inval = res['1'];
            var alt = $(this).attr('alt');
            alert(alt);
            $.ajax({
                type: 'post',
                url: "{{ admin_url('incident/initial-incident/fetchEmployeeDetails') }}",
                method: 'POST',
                data: {
                    injury_id: inval,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    //alert(data);
                    var empdat = JSON.parse(data);
                    //alert(empdat['empdata']['imgMapdata']);
                    //alert(empdat['empdata']['body_parts']);
                    if (empdat['empdata'] != null) {
                        $("#bp" + inval).html(empdat['empdata']['body_parts']);
                    }
                }
            });
        });

        $(document).on('click', '.injury-btn', function() {

            var getid = $(this).data('id');
            var inj_id = $(this).data('injid');
            var injuredPerson_type = $('#RowInjTypedata_' + getid).val();
            var injuredPerson_emp = $('#RowInjEmpdata_' + getid).val();
            var injuredPerson_others = $('#RowInjothersdata_' + getid).val();
            var injuredPerson_empName = $('#RowInjothersdata_' + getid).val();

            var errorcount = '0';
            var injuredPerson = '0';
            var injury_person_type = '0';

            if ((injuredPerson_emp == '' || injuredPerson_emp == null) && (injuredPerson_empName == '' ||
                    injuredPerson_empName == null)) {
                Swal.fire('Error', 'Please Select Victim Name', 'error');
                errorcount = '1';
            } else {
                errorcount = '0';
                if (injuredPerson_emp != '') {
                    injuredPerson = injuredPerson_emp;
                    injury_person_type = injuredPerson_type;
                } else {
                    injuredPerson = injuredPerson_empName;
                    injury_person_type = injuredPerson_type;
                }
            }

            if (errorcount == '1') {

                return false;
            } else {

                $('#injuredPerson').val(injuredPerson);
                $('#injury_person_type').val(injury_person_type);
                var random_id = $('#random_id').val();
                var acc_prim_add = $('#acc_prim_add').val();

                var emp_details = get_emp_details_by_id(injuredPerson, random_id, acc_prim_add, inj_id,
                    injuredPerson_type);


                $("#injury_model [name='injperson']").val(injuredPerson);
                $("#injury_model").modal("show");
            }

        });

        function get_emp_details_by_id(injuredPerson, random_id, acc_prim_add, inj_id, injuredPerson_type) {
            var url = "{{ admin_url('incident/initial-incident/investigation/getbodyEmpdetails') }}";
            var data = {
                partyname: injuredPerson,
                random_id: random_id,
                acc_prim_add: acc_prim_add,
                injuredPerson_type: injuredPerson_type,
            };

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(data) {
                    console.log(data); // Inspect the response
                    if (data['empdata'] && data['empdata'].length > 0) {
                        $.each(data['empdata'], function(i, emp) {
                            $("#imgMapdata1").val(emp['imgMapdata']);
                            $("#body_prim_id").val(emp['id']);
                            $("#incident_id").val(0);
                            $("#injury_id").val(inj_id);
                        });
                    } else {
                        $("#body_prim_id").val(0);
                        $("#incident_id").val(0);
                        $("#injury_id").val(inj_id);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $('.clearbodyparts').on('click', function() {
            var bpid = $("#injury_model [name='inc_body_id']").val();
            var injurydetails = $("#injury_model [name='injurydetails']").val();
            //alert(bpid);
            if (bpid == '') {

            } else {
                //alert(bpid);
                $.ajax({
                    type: 'post',
                    url: "{{ admin_url('incident/initial-incident/deletebodayparts') }}",
                    method: 'POST',
                    data: {
                        bid: bpid,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        //alert(data);
                        //  if(data == 1){
                        $('#text_injbodypart_' + injurydetails).val('');
                        $("#injury_model").modal("hide");
                        // location.reload();
                        //}
                    }

                });
            }
        });

        function clearInjuryBasicDetails() {

            var modalsrc = $("#injury_model");

            $(modalsrc).find("[name='imgMapdata1']").val('');
            $(modalsrc).find(':input[name="save_inj"]').prop('disabled', false);
            $(modalsrc).find(':input[name="draft_inj"]').prop('disabled', false);
            $('.others').addClass('hide');

        }

        $("document").ready(function(e) {

            window.trigger = this;
            //clearInjuryBasicDetails();
            /* var table = $('.injtable');
                        var tableRows = table.find('tr').length;
                        console.log(tableRows);
            
                        if (tableRows > 10) {
                            $('.injtable').css('overflow-y', 'auto');
                            $('.injtable').css('max-height', '300px'); // Example maximum height
                        }*/

            var alt = $(this).attr('alt');

            var injuredPerson = $('#employeeInvesName' + alt).val();
            if (injuredPerson == '') {

                Swal.fire('Error', 'Please Select Victim Name', 'error');

            } else {

                empcourse = [];
                var errorcount = '0';

                $('.invesEmployeeName ').each(function(i, obj) {

                    var newstring = ''

                    empname = this.value;
                    if (empname == '') {
                        name = '';
                    } else {
                        name = $(this).val();

                    }

                    coursename = $(this).closest("div.row").find("#employeeInvesName" + i).val();

                    if (errorcount == '0') {

                        if (empname != '' && coursename != '') {


                            var newstring = this.value + '_' + coursename;



                            if (jQuery.inArray(newstring, empcourse) > -1) {

                                Swal.fire('Error', 'Details on the Victim Employee Name already selected',
                                    'error');
                                errorcount = '1';


                            } else {
                                empcourse.push(newstring);

                            }
                        }

                    }
                });


                if (errorcount == '1') {

                    return false;
                } else {


                    $('#injuredPerson').val(injuredPerson);

                    $("#injury_model [name='injperson']").val(injuredPerson);
                    $("#injury_model").modal("show");


                }



            }


        });

        $("#injury_model").on("shown.bs.modal", function() {
            $(window).resize();
        });


        $(document).on('click', '.addbodyparts', function() {
            var allFilled = true;
            $(".img-desc .des_injury1_img").each(function(i, ele) {
                if ($(ele).val() == '') {
                    allFilled = false;
                    return false; // Exit the loop early
                }
            });

            if (!allFilled) {
                Swal.fire('Alert', 'Please Enter The Body Parts Descriptions', 'Alert');
            } else {
                Swal.fire('Alert', 'Please Select Body Parts', 'Alert');
            }



        });

        $('img[usemap]').rwdImageMaps();
        window.mapEdit = false;
        window.canvas_obj = {};
        $("#injury_model").on("shown.bs.modal", function() {
            setTimeout(function() {
                $(".img-map").css("opacity", "0");
                $(".img-map").html($("#tmp-male").html())

                editInjuryDetails();
                setTimeout(function() {
                    $('div.img-content div.img-map img[usemap]').rwdImageMaps();
                    myInit1($("div.img-content div.img-map img"), 1);

                    triggerMapClick1();
                }, 200);
                setTimeout(function() {
                    $(".img-map").css("opacity", "1");
                }, 290);


            }, 300);

            $.each(window.canvas_obj, function(canvas, canvasObj) {
                canvasClear($("#" + canvas)[0]);
            });
            $("div.image-container .parts").hide();

        }).on("hidden.bs.modal", function() {

            $("area[desc]").each(function(i, ele) {

                $(ele).removeAttr("desc");
            });
            $(".img-desc").html('');
            $("div.image-container .parts").hide();
            for (i = 0; i < 10; i++) {
                window.clearTimeout(i);
            }
            $('#imgMapdata1').val('');
            $(".img-map").css("opacity", "0");

        });

        $('.gender').change(function() {


        });

        function editInjuryDetails() {

            var json = $('#imgMapdata1').val();
            if (json != undefined && json != '') {
                json = JSON.parse(json);
                $.each(json, function(name, value) {
                    switch (name) {
                        case 'map':
                            mapTrigger(value);

                            break;
                        default:

                            break;
                    }
                });
            }
        }

        $("div.image-container .parts").hide();

        //assign iage map json to hidden input


        $('#injuryform').submit(function(e) {

            var data = getAllValues();
            data['map'] = getMapValues();
            var data1 = JSON.stringify(data);

            $('#imgMapdata1').val(data1);


        });


        function getAllValues(data) {
            var data = {};
            return data;
        }

        function getMapValues() {

            var data1 = {},
                cmap, pmap;
            $('body .des_injury1_img').each(function(i, ele) {

                pmap = $(ele).attr('data-point1');
                cmap = $(ele).attr('data-point2');
                if (!(pmap in data1)) {
                    data1[pmap] = {};
                }
                if (!(cmap in data1[pmap])) {
                    data1[pmap][cmap] = {};
                }
                data1[pmap][cmap] = $(ele).val();
            });
            //console.log(data1);
            return data1;
        }



        function triggerMapClick1() {

            $("div.image-container .img-map-parts map area,div.img-content div.img-map map area").unbind(
                'click');
            $("div.image-container .img-map-parts map area,div.img-content div.img-map map area").click(
                function() {
                    var canvas = $(this).closest("div").find("canvas")[0],
                        txtarea, addtxt;
                    canvas = $("#image2_canvas")[0];
                    var i = 1;
                    if ($(this).parent().attr("data-type") === 'total') {

                        canvas = $("#image1_canvas")[0];
                        myLeave(canvas, 1);
                    }

                    myLeave(canvas);
                    descBox(this, i);
                    initDescriptionJs();
                    var coordStr = $(this).attr('coords');
                    var areaType = $(this).attr('shape');
                    switch (areaType) {
                        case 'polygon':
                        case 'poly':
                            drawPoly1(coordStr, canvas);
                            break;
                        case 'rect':
                            drawRect1(coordStr, canvas);
                    }

                    i++;

                });
        }

        function descBox1(point1, point2, desc) {
            var is_ready_only = '{{ $is_ready_only }}';
            var div, label, point1, point2;
            var j = 0;
            div = $("<div/>");
            label = $("<label/>").text("" + $('[data-map~="' + point1 + '"] [data-map~="' + point2 + '"]').attr(
                    "title"))
                .addClass("desc-label").attr('id', "desc-label" + j);
            var tit = $(label).text();

            //console.log('gfdgf:')
            //console.log($('[data-map~="'+point1+'"]'))

            if (is_ready_only != 1) {
                txtarea = $("<textarea/>", {
                    'data-point1': point1,
                    'data-point2': point2,
                    "class": 'form-control des_injury1_img',
                    "id": 'des_img' + j,
                    "alt": j,

                });
            } else {
                txtarea = $("<textarea/>", {
                    'data-point1': point1,
                    'data-point2': point2,
                    "class": 'form-control des_injury1_img',
                    "id": 'des_img' + j,
                    "alt": j,
                    "style": 'pointer-events: none;',
                });
            }

            if (is_ready_only != 1) {
                buttons = $(
                    '<div><button type="button" class="fa fa-trash-o deletes" style="color:red;" alt="' +
                    j +
                    '" id="deletes' + j + '" title="' + tit + '"></button></div>');
            } else {
                buttons = '';
            }

            addtxt = true;
            if (j == 0) {
                j = 1;
            }
            $(".img-desc .des_injury1_img").each(function(i, ele) {
                if ($(ele).val() == '') {
                    addtxt = false;
                    $(ele).attr("data-point1", point1).attr("data-point2", point2);
                }

                label = $("<label/>").text("" + $('[data-map~="' + point1 + '"] [data-map~="' + point2 +
                        '"]').attr(
                        "title"))
                    .addClass("desc-label" + j).attr('id', "desc-label" + j);

                if (is_ready_only != 1) {
                    txtarea = $("<textarea/>", {
                        'data-point1': point1,
                        'data-point2': point2,
                        "class": 'form-control des_injury1_img',
                        "id": 'des_img' + j,
                        "alt": j,

                    });
                } else {
                    txtarea = $("<textarea/>", {
                        'data-point1': point1,
                        'data-point2': point2,
                        "class": 'form-control des_injury1_img',
                        "id": 'des_img' + j,
                        "alt": j,
                        "style": 'pointer-events: none;',
                    });
                }


                if (is_ready_only != 1) {
                    buttons = $(
                        '<div><button type="button" class="fa fa-trash-o deletes" style="color:red;" alt="' +
                        j + '" id="deletes' + j + '" title="' + tit + '"></button></div>');
                    j++;
                } else {
                    buttons = '';
                }

            });

            if (addtxt) {
                $(div).append(label).append(buttons)
                $(div).append(label).append(txtarea)
                $(".img-desc").append(div);
            } else {
                $(".img-desc div:last-child .desc-label")
                    .text("" + $(area).attr("title"));
            }
            if (desc != undefined) {
                $(txtarea).text(desc).val(desc);
            }

        }

        function descBox(area, k, desc) {


            var div, label, point1, point2, buttons, txtarea;
            var j = 0;
            var tit = $(area).attr("title");
            div = $("<div/>");
            label = $("<label/>").text("" + $(area).attr("title"))
                .addClass("desc-label").attr('id', "desc-label" + j);

            point2 = $(area).attr("data-map");
            point1 = $(area).parent().attr("data-map");
            txtarea = $("<textarea/>", {
                'data-point1': point1,
                'data-point2': point2,
                "class": 'form-control des_injury1_img',
                "id": 'des_img' + j,
                "alt": j,

            });

            buttons = $('<div><button type="button" class="fa fa-trash-o deletes" style="color:red;" alt="' +
                j +
                '" id="deletes' + j + '" title="' + tit + '"></button></div>');


            addtxt = true;
            if (j == 0) {
                j = 1;
            }
            $(".img-desc .des_injury1_img").each(function(i, ele) {
                if ($(ele).val() == '') {
                    addtxt = false;
                    $(ele).attr("data-point1", point1).attr("data-point2", point2);

                }

                label = $("<label/>").text("" + $(area).attr("title"))
                    .addClass("desc-label").attr('id', "desc-label" + j);


                txtarea = $("<textarea/>", {
                    'data-point1': point1,
                    'data-point2': point2,
                    "class": 'form-control des_injury1_img',
                    "id": 'des_img' + j,
                    "alt": j,

                });
                buttons = $(
                    '<div><button type="button" class="fa fa-trash-o deletes" style="color:red;" alt="' +
                    j + '" id="deletes' + j + '" title="' + tit + '"></button></div>');
                j++;
            });
            if ($(area).attr("ref") == undefined) {
                if (addtxt) {

                    $(div).append(label).append(txtarea)
                    $(".img-desc").append(div);
                    $(div).append(txtarea).append(buttons)

                } else {
                    $(".img-desc div:last-child .desc-label").text("" + $(area).attr("title"));
                }
            }

            if (desc != undefined) {
                $(txtarea).text(desc).val(desc);
            }

        }

        $(document).on('click', '.deletes', function() {
            var photoimgDivss = $('.deletes');
            if (photoimgDivss.length > 1) {
                var alt = $(this).attr('alt');
                $('#des_img' + alt).val('');

                if ($('#des_img' + alt).attr("data-point1") != undefined) {
                    //map
                    var point1 = $("[data-map='" + $('#des_img' + alt).attr("data-point1") + "']");
                    if (point1.length == 0) {

                        return;
                    }

                    //area
                    var point2 = $(point1).find("[data-map='" + $('#des_img' + alt).attr(
                        "data-point2") + "']");
                    //console.log(point2);
                    if (point2.length == 0) {
                        return;
                    }

                    var canvas = $(point2).closest("div").find("canvas")[1];
                    //  canvas = $("#image2_canvas_marked")[0];
                    if ($(point1).attr("data-map") == "total") {
                        canvas = $("#image1_canvas_marked")[0];
                    }

                    if ($(point1).attr("data-map") == "head") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='one']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "hand-right") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='eleven']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "hand-left") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twelve']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "foot-left") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twentyseven']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "foot-right") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twentyeight']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    }

                    // console.log(point1);
                    $(point2).attr("desc", $('#des_img' + alt).val());
                    if ($('#des_img' + alt).val() == '') {
                        $(point2).removeAttr("desc");
                    }

                    var parentClass = $(point2).parent().parent().attr('class').split(' ');
                    //changeImage('.' + parentClass[1]);
                    if (parentClass[0] == 'parts') {
                        $('#des_img' + alt).blur(function() {
                            changeImage('.' + parentClass[1]);
                        });
                    } else {
                        //   console.log(canvas);
                        //   console.log(point1);
                        mapRebuilt(canvas, point1);
                    }
                }

                $('#desc-label' + alt).remove();
                $('#desc-label' + alt).hide();

                $('#des_img' + alt).remove();
                $('#des_img' + alt).hide();

                $('#deletes' + alt).remove();
                $('#deletes' + alt).hide();
            } else {
                Swal.fire('Sorry', 'Image cannot be empty', 'warning');
            }




        })

        function initDescriptionJs() {

            $(".des_injury1_img").change(function() {

                if ($(this).attr("data-point1") != undefined) {
                    //map
                    var point1 = $("[data-map='" + $(this).attr("data-point1") + "']");
                    if (point1.length == 0) {

                        return;
                    }

                    //area
                    var point2 = $(point1).find("[data-map='" + $(this).attr("data-point2") + "']");
                    //console.log(point2);
                    if (point2.length == 0) {
                        return;
                    }

                    var canvas = $(point2).closest("div").find("canvas")[1];
                    //  canvas = $("#image2_canvas_marked")[0];
                    if ($(point1).attr("data-map") == "total") {
                        canvas = $("#image1_canvas_marked")[0];
                    }

                    if ($(point1).attr("data-map") == "head") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='one']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "hand-right") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='eleven']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "hand-left") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twelve']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "foot-left") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twentyseven']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    } else if ($(point1).attr("data-map") == "foot-right") {
                        canvas = $("#image1_canvas_marked")[0];
                        $("[data-map~='total'] [data-map~='twentyeight']").attr("desc", "abc");
                        myLeave(canvas);
                        canvasMark($("map[data-map='total']"));
                    }

                    // console.log(point1);
                    $(point2).attr("desc", $(this).val());
                    if ($(this).val() == '') {
                        $(point2).removeAttr("desc");
                    }

                    var parentClass = $(point2).parent().parent().attr('class').split(' ');
                    //changeImage('.' + parentClass[1]);
                    if (parentClass[0] == 'parts') {
                        $(this).blur(function() {
                            changeImage('.' + parentClass[1]);
                        });
                    } else {
                        mapRebuilt(canvas, point1);
                    }
                }
            });
        }

        function mapTrigger(json, isString = false) {

            if (isString) {
                json = JSON.parse(json);
            }

            myInit1($("div.img-content div.img-map img"), 1);
            var map, flag = 0;
            // console.log(json);
            $.each(json, function(key, area) {
                map = $('[data-map~=' + key + ']');
                switch (key) {
                    case 'head':
                        $("[data-map~='total'] [data-map~='one']").attr("desc", "abc");
                        break;
                    case 'hand-right':
                        $("[data-map~='total'] [data-map~='eleven']").attr("desc", "abc");
                        break;
                    case 'hand-left':
                        $("[data-map~='total'] [data-map~='twelve']").attr("desc", "abc");
                        break;
                    case 'foot-right':
                        $("[data-map~='total'] [data-map~='twentyeight']").attr("desc", "abc");
                        break;
                    case 'foot-left':
                        $("[data-map~='total'] [data-map~='twentyseven']").attr("desc", "abc");
                        break;
                }


                $.each(area, function(akey, desc) {

                    //console.log("akey: "+akey);
                    $(map).find('[data-map~=' + akey + ']').attr("desc", desc);
                    descBox1(key, akey, desc);
                });
                $(map).find('area[desc]').each(function(i, area) {
                    //console.log(area);
                    // descBox(area, $(area).attr('desc'));
                });
            });
            setTimeout(function() {
                $(".des_injury1_img").change();
            }, 300);
            initDescriptionJs();

            window.mapEdit = true;
            canvasMark($("map[data-map='total']"));
        }

        function changeImage(part) {
            $("div.image-container .img-map-parts .parts").hide();
            if (part != undefined) {
                $("div.image-container .img-map-parts .parts" + part).show();
                $('div.image-container .img-map-parts').prepend($("div.image-container .img-map-parts .parts" +
                    part));
            }

            var img = $("div.image-container .img-map-parts .parts:visible img");

            $('.image-container .img-map-parts .parts' + part + ':visible img[usemap]').rwdImageMaps();
            if (img.length != 0) {
                myInit1(img);
                setTimeout(function() {
                    triggerMapClick1();
                }, 500);

            }

            if ('image2_canvas' in window.canvas_obj) {
                canvasClear($("#image2_canvas")[0]);
                canvasClear($("#image2_canvas_marked")[0]);
            }


            var map = $(img).closest("div").find("map");

            if (part != '') {

                if ('image2_canvas' in window.canvas_obj) {

                    setTimeout(function() {

                        mapRebuilt($("#image2_canvas")[0], map);
                    }, 1);
                }
                return;
            }

        }


        function myInit1(image, type = 2, callback) {
            // get the target image
            var img = $(image)[0];
            var x, y, w, h;
            // get it's position and width+height
            x = img.offsetLeft;
            y = img.offsetTop;
            w = img.clientWidth;
            h = img.clientHeight;
            // move the canvas, so it's contained by the same parent as the image
            //var can = $('#myCanvas')[0];
            //$(img).parent().append(can);

            var color, canvas_name, canvas_count;
            canvas_count = 2;
            for (i = 0; i < canvas_count; i++) {
                color = 'rgba(255, 165, 0, 0.69)';
                canvas_name = "#image" + type + "_canvas";
                //alert(canvas_name);
                if (i == 1) {
                    color = 'rgba(255, 0, 0, 0.65)';
                    canvas_name = "#image" + type + "_canvas_marked";
                    //alert(canvas_name);
                }
                //can = $(img).parent().find("canvas")[i];
                can = $(canvas_name)[0];
                // place the canvas in front of the image
                can.style.zIndex = 1;
                // position it over the image
                can.style.left = x + 'px';
                can.style.top = y + 'px';
                // make same size as the image
                can.setAttribute('width', w + 'px');
                can.setAttribute('height', h + 'px');
                // get it's context
                window.canvas_obj[$(can).attr("id")] = can.getContext('2d');
                // console.log(window.canvas_obj[$(can).attr("id")]);
                // set the 'default' values for the colour/width of fill/stroke operations
                window.canvas_obj[$(can).attr("id")].fillStyle = color;
                window.canvas_obj[$(can).attr("id")].strokeStyle = color;
                window.canvas_obj[$(can).attr("id")].lineWidth = 2;
                $(window).resize();
                if (callback != undefined) {
                    eval(callback);
                }
            }

        }

        function canvasClear(canvas) {

            return window.canvas_obj[$(canvas).attr("id")].clearRect(0, 0, canvas.width, canvas.height);
        }

        function mapRebuilt(canvas, map) {
            window.canvas_obj[$(canvas).attr("id")].clearRect(0, 0, canvas.width, canvas.height);
            canvasMark(map)
        }

        function canvasMark(map) {
            var type = 2;
            if ($(map).attr("data-map") == "total") {
                type = 1;
            }

            $(map).find("area[desc]").each(function(i, area) {
                var coordStr = $(area).attr('coords');
                var canvas = $("#image" + type + "_canvas_marked")[0];
                drawPoly1(coordStr, canvas);
            });
        }

        function myLeave(src, type = 0) {
            var canvas;
            var canvas_name = "#image1_canvas";
            if (type == 1) {
                canvas_name = "#image2_canvas";
            }
            canvas = $(canvas_name)[0];
            //canvas = $(src).closest("div").find("canvas");
            canvas = src;
            window.canvas_obj[$(canvas).attr("id")].clearRect(0, 0, canvas.width, canvas.height);
        }

        function drawPoly1(coOrdStr, canvas) {
            var mCoords = coOrdStr.split(',');
            var i, n;
            n = mCoords.length;
            window.canvas_obj[$(canvas).attr("id")].beginPath();
            window.canvas_obj[$(canvas).attr("id")].moveTo(mCoords[0], mCoords[1]);
            for (i = 2; i < n; i += 2) {
                window.canvas_obj[$(canvas).attr("id")].lineTo(mCoords[i], mCoords[i + 1]);
            }
            window.canvas_obj[$(canvas).attr("id")].lineTo(mCoords[0], mCoords[1]);
            window.canvas_obj[$(canvas).attr("id")].closePath();
            window.canvas_obj[$(canvas).attr("id")].fill();
        }

        function drawRect1(coOrdStr, canvas) {
            var mCoords = coOrdStr.split(',');
            var top, left, bot, right;
            left = mCoords[0];
            top = mCoords[1];
            right = mCoords[2];
            bot = mCoords[3];
            window.canvas_obj[$(canvas).attr("id")].strokeRect(left, top, right - left, bot - top);
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

            });

            $("#injuryform").validate({
                submitHandler: function(form) {

                    var data = getAllValues();
                    data['map'] = getMapValues();

                    var data1 = JSON.stringify(data);



                    $('#injury_body_parts_' + $('#injurydetails').val()).val(data1);
                    var descLabels = $(".desc-label");

                    var labelTextArray = [];

                    var labelTextArray1 = [];

                    var aler = '';



                    descLabels.each(function() {

                        aler = $(this).parent().find('textarea').val();

                        labelTextArray.push($(this).text() + ': ' + aler);

                        labelTextArray1.push($(this).text());

                    });
                    $('#imgMapdata1').val(data1);
                    var commaSeparatedText = labelTextArray.join(",");

                    var commaSeparatedText1 = labelTextArray1.join(",");

                    $('#humanbodyinjury1').val(commaSeparatedText);

                    $('#humanbodyinjurylabel1').val(commaSeparatedText1);



                    var formDatas = $('#injuryform').serialize();


                    var imgdata = $('#injuryform').serializeArray();

                    if (imgdata[1]['name'] == "imgMapdata" && imgdata[1]['value'] ==
                        '{"map":{}}') {
                        Swal.fire('Error', 'Please Select Body Parts', 'error');

                    } else {

                        var allFilleddesc = true;
                        $(".img-desc .des_injury1_img").each(function(i, ele) {
                            if ($(ele).val() == '') {
                                allFilleddesc = false;
                                return false; // Exit the loop early
                            }
                        });

                        if (!allFilleddesc) {
                            Swal.fire('Error', 'Please Enter The Body Parts Descriptions',
                                'error');
                        } else {

                            var url =
                                "{{ admin_url('incident/initial-incident/addInjury/api') }}";

                            $("#bodypartimage").val("");
                            const image = document.getElementById('img-imgmap1');
                            const canvas = document.getElementById('image1_canvas_marked');

                            const tempCanvas = document.createElement('canvas');

                            const tempCtx = tempCanvas.getContext('2d');
                            tempCanvas.width = canvas.width;
                            tempCanvas.height = canvas.height;

                            tempCtx.drawImage(image, 0, 0);
                            tempCtx.drawImage(canvas, 0, 0);

                            const combinedImageUrl = tempCanvas.toDataURL('image/png');
                            $("#bodypartimage").val(combinedImageUrl);
                            console.log(combinedImageUrl);

                            var random_id = $("#random_id").val();
                            var formDatas = new URLSearchParams($('#injuryform')
                                .serialize());
                            formDatas.append('random_id',
                                random_id); // Append the new key-value pair
                            var data = formDatas.toString()

                            $.ajax({
                                type: 'ajax',
                                dataType: 'json',
                                method: 'post',
                                data: data,
                                url: url,
                                success: function(data) {
                                    $('.alert-msg').html(
                                        '<span style="color:green;">Body Part Saved Successfully!</span>'
                                    );
                                    $(".alert-msg").show().delay(3000)
                                        .fadeOut();
                                    setTimeout(function() {
                                        $("#injury_model").modal(
                                            'hide');
                                        setTimeout(function() {}, 500);
                                    }, 1000);

                                    var myModal = $('#injury_model').on('shown',
                                        function() {
                                            clearTimeout(myModal.data(
                                                'hideInteval'))
                                            var id = setTimeout(function() {
                                                myModal.modal(
                                                    'hide');
                                            });
                                        })

                                }
                            });
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>

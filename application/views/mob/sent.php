<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Iconnect | Send feedback</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Dancing+Script:400,700);

        * {
            box-sizing: border-box;
        }

        html {
            overflow: auto;

            height: 100%;
            color: white;
            /*Image only BG fallback*/
            background: url('<?php echo base_url(); ?>assets/mobile/gs.png');
            /*background = gradient + image pattern combo*/
            background: linear-gradient(rgba(196, 102, 0, 0.2), rgba(155, 89, 182, 0.2)),
            url('<?php echo base_url(); ?>assets/mobile/gs.png');
            text-align: center;

        }

        h1, p {
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 2rem;
            font-family: 'Dancing Script';
        }

        small {
            display: block;
            padding: 1rem 0;
            font-size: 0.8rem;
            -webkit-transition: opacity 0.33s;
            transition: opacity 0.33s;
        }

        textarea, input, button {
            line-height: 1.5rem;
            border: 0;
            outline: none;
            font-family: inherit;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        textarea, input {
            color: #4e5e72;
            background-color: transparent;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='24'><rect fill='rgb(229, 225, 187)' x='0' y='23' width='10' height='1'/></svg>");
        }

        textarea {
            width: 100%;
            height: 8rem;
            resize: none;
        }

        input {
            width: 50%;
            margin-bottom: 1rem;
        }

        input[type=text]:invalid, input [type=email]:invalid {
            box-shadow: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='24'><rect fill='rgba(240, 132, 114, 0.5)' x='0' y='23' width='10' height='1'/></svg>");
        }

        button {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            background-color: rgba(78, 94, 114, 0.9);
            color: white;
            font-size: 1rem;
            -webkit-transition: background-color 0.2s;
            transition: background-color 0.2s;
        }

        button:hover, button :focus {
            outline: none;
            background-color: rgba(78, 94, 114, 1);
        }

        input[type=text]:focus,
        input[type=email]:focus,
        textarea:focus {
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='24'><rect fill='rgba(78, 94, 114, 0.3)' x='0' y='23' width='10' height='1'/></svg>");
            outline: none;
        }

        .wrapper {
            width: 35rem;
            background-color: white;
        }

        .letter {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            width: 30rem;
            margin: auto;
            -webkit-perspective: 60rem;
            perspective: 60rem;
        }

        .side {
            height: 12rem;
            background-color: #fcfcf8;
            outline: 1px solid transparent;
        }

        .side:nth-of-type(1) {
            padding: 2rem 2rem 0;
            border-radius: 1rem 1rem 0 0;
            box-shadow: inset 0 0.75rem 2rem rgba(229, 225, 187, 0.5);
        }

        .side.side:nth-of-type(2) {
            padding: 2rem;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 0.3rem 0.3rem rgba(0, 0, 0, 0.05), inset 0 -0.57rem 2rem rgba(229, 225, 187, 0.5);
            text-align: right;
        }

        .envelope {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            margin: auto;
        }

        .envelope.front {
            width: 10rem;
            height: 6rem;
            border-radius: 0 0 1rem 1rem;
            overflow: hidden;
            z-index: 9999;
            opacity: 0;
        }

        .envelope.front::before, .envelope.front::after {
            position: absolute;
            display: block;
            width: 12rem;
            height: 6rem;
            background-color: #e9dc9d;
            -webkit-transform: rotate(30deg);
            transform: rotate(30deg);
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
            content: '';
        }

        .envelope.front::after {
            right: 0;
            -webkit-transform: rotate(-30deg);
            transform: rotate(-30deg);
            -webkit-transform-origin: 100% 0;
            transform-origin: 100% 0;
        }

        .envelope.back {
            top: -4rem;
            width: 10rem;
            height: 10rem;
            overflow: hidden;
            z-index: -9998;
            opacity: 0;
            -webkit-transform: translateY(-6rem);
            transform: translateY(-6rem);
        }

        .envelope.back::before {
            display: block;
            width: 10rem;
            height: 10rem;
            background-color: #e9dc9d;
            border-radius: 1rem;
            content: '';
            -webkit-transform: scaleY(0.6) rotate(45deg);
            transform: scaleY(0.6) rotate(45deg)
        }

        .result-message {
            opacity: 0;
            -webkit-transition: all 0.3s 2s;
            transition: all 0.3s 2s;
            -webkit-transform: translateY(9rem);
            transform: translateY(9rem);
            z-index: -9999;
        }

        .sent .letter {
            -webkit-animation: scaleLetter 1s forwards ease-in /*,
                       pushLetter 0.5s 1.33s forwards ease-out*/;
            animation: scaleLetter 1s forwards ease-in /*,
                       pushLetter 0.5s 1.33s forwards ease-out*/;
        }

        .sent .side:nth-of-type(1) {
            -webkit-transform-origin: 0 100%;
            transform-origin: 0 100%;
            -webkit-animation: closeLetter 0.66s forwards ease-in;
            animation: closeLetter 0.66s forwards ease-in;
        }

        .sent .side:nth-of-type(1) h1, .sent .side:nth-of-type(1) textarea {
            -webkit-animation: fadeOutText 0.66s forwards linear;
            animation: fadeOutText 0.66s forwards linear;
        }

        .sent button {
            background-color: rgba(78, 94, 114, 0.2);
        }

        .sent .envelope {
            -webkit-animation: fadeInEnvelope 0.5s 1.33s forwards ease-out;
            animation: fadeInEnvelope 0.5s 1.33s forwards ease-out;
        }

        .sent .result-message {
            opacity: 1;
            -webkit-transform: translateY(12rem);
            transform: translateY(12rem);
        }

        .sent small {
            opacity: 0;
        }

        .centered {
            position: absolute;
            left: 0;
            right: 0;
            margin: 1rem auto;
        }

        @-webkit-keyframes closeLetter {
            50% {
                -webkit-transform: rotateX(-90deg);
                transform: rotateX(-90deg);
            }
            100% {
                -webkit-transform: rotateX(-180deg);
                transform: rotateX(-180deg);
            }
        }

        @keyframes closeLetter {
            50% {
                -webkit-transform: rotateX(-90deg);
                transform: rotateX(-90deg);
            }
            100% {
                -webkit-transform: rotateX(-180deg);
                transform: rotateX(-180deg);
            }
        }

        @-webkit-keyframes fadeOutText {
            49% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }

        @keyframes fadeOutText {
            49% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }

        @-webkit-keyframes fadeInEnvelope {
            0% {
                opacity: 0;
                -webkit-transform: translateY(8rem);
                transform: translateY(8rem);
            }
            /*90% {opacity: 1; transform: translateY(4rem);}*/
            100% {
                opacity: 1;
                -webkit-transform: translateY(4.5rem);
                transform: translateY(4.5rem);
            }
        }

        @keyframes fadeInEnvelope {
            0% {
                opacity: 0;
                -webkit-transform: translateY(8rem);
                transform: translateY(8rem);
            }
            /*90% {opacity: 1; transform: translateY(4rem);}*/
            100% {
                opacity: 1;
                -webkit-transform: translateY(4.5rem);
                transform: translateY(4.5rem);
            }
        }

        @-webkit-keyframes scaleLetter {
            66% {
                -webkit-transform: translateY(-8rem) scale(0.5, 0.5);
                transform: translateY(-8rem) scale(0.5, 0.5);
            }
            75% {
                -webkit-transform: translateY(-8rem) scale(0.5, 0.5);
                transform: translateY(-8rem) scale(0.5, 0.5);
            }
            90% {
                -webkit-transform: translateY(-8rem) scale(0.3, 0.5);
                transform: translateY(-8rem) scale(0.3, 0.5);
            }
            97% {
                -webkit-transform: translateY(-8rem) scale(0.33, 0.5);
                transform: translateY(-8rem) scale(0.33, 0.5);
            }
            100% {
                -webkit-transform: translateY(-8rem) scale(0.3, 0.5);
                transform: translateY(-8rem) scale(0.3, 0.5);
            }
        }

        @keyframes scaleLetter {
            66% {
                -webkit-transform: translateY(-8rem) scale(0.5, 0.5);
                transform: translateY(-8rem) scale(0.5, 0.5);
            }
            75% {
                -webkit-transform: translateY(-8rem) scale(0.5, 0.5);
                transform: translateY(-8rem) scale(0.5, 0.5);
            }
            90% {
                -webkit-transform: translateY(-8rem) scale(0.3, 0.5);
                transform: translateY(-8rem) scale(0.3, 0.5);
            }
            97% {
                -webkit-transform: translateY(-8rem) scale(0.33, 0.5);
                transform: translateY(-8rem) scale(0.33, 0.5);
            }
            100% {
                -webkit-transform: translateY(-8rem) scale(0.3, 0.5);
                transform: translateY(-8rem) scale(0.3, 0.5);
            }
        }

        /*
        @keyframes pushLetter {
          0% {transform: translateY(-8rem) scale(0.3, 0.5);}
          50% {transform: translateY(-8rem) scale(0.3, 0.5);}
          90% {transform: translateY(-8.5rem) scale(0.3, 0.5);}
          100% {transform: translateY(-8rem) scale(0.3, 0.5);}
        }
        */
    </style>



    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>



</head>

<body translate="no">
<div class="wrapper centered">
    <article class="letter">
        <div class="side">
            <p>
            </p>
        </div>
        <div class="side">
            <p>
            </p>
            <p>
            </p>
            <p>
                <button style="visibility: hidden;" id="sendLetter">Send</button>
            </p>
        </div>
    </article>
    <div class="envelope front"></div>
    <div class="envelope back"></div>
</div>
<p class="result-message centered">Thank you for your time, We recieved your feedback :)</p>
<script src="<?php echo base_url(); ?>assets/mobile/jquery-1.9.1.min.js" type="text/javascript"></script>

<script>
    function addClass() {
        document.body.classList.add('sent');
    }

    sendLetter.addEventListener('click', addClass);
    //# sourceURL=pen.js
    $(document).ready(function () {
        $('#sendLetter').trigger("click");
    });
</script>




</body>
</html>
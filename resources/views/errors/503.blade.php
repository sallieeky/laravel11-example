<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 Service Unavailable - {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="/images/favicon.ico" />

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type="radio"] {
            display: none;
        }
        #cheat {
            position: absolute;
            bottom: 0;
            right: 0;
        }
        #cheat:checked ~ input[type="radio"] {
            display: block;
        }
        #cheat:checked ~ input[type="radio"]::after {
            content: attr(id);
            color: #fff;
            display: block;
            padding-left: 1em;
            width: 30em;
        }
        #a-up,
        #a-left,
        #b-up,
        #b-center,
        #c-up,
        #c-right,
        #d-middle,
        #d-left,
        #e-middle,
        #e-center,
        #f-middle,
        #f-right,
        #g-down,
        #g-left,
        #h-down,
        #h-center {
            outline: 2px solid red;
        }
        .board {
            font-size: 1vmin;
            border: 8px solid #c7ddf0;
            border-radius: 3em;
            width: 60em;
            height: 60em;
            position: relative;
            margin: auto;
            overflow: hidden;
        }
        [class^="peice"] {
            --x: 20em;
            position: absolute;
            width: 20em;
            height: 20em;
            transform: translate(var(--x), var(--y));
            transition: transform 0.5s;
        }
        [class^="peice"].img {
            background-size: 60em 60em;
            border-radius: 2em;
            box-shadow: inset 0 0 0em 0.2em #eee, inset 1em 1em 1em #eee5,
                inset -1em -1em 1em #0005;
            box-sizing: box-border;
        }
        .peice-a.img {
            background-position: top left;
        }
        .peice-b.img {
            background-position: top center;
        }
        .peice-c.img {
            background-position: top right;
        }
        .peice-d.img {
            background-position: center left;
        }
        .peice-e.img {
            background-position: center center;
        }
        .peice-f.img {
            background-position: center right;
        }
        .peice-g.img {
            background-position: bottom left;
        }
        .peice-h.img {
            background-position: bottom center;
        }
        [class^="peice"] label {
            display: block;
            width: 13em;
            height: 13em;
            position: absolute;
            transform: rotate(45deg);
            background: #c7ddf0;
        }
        [class^="peice"] label:hover {
            background: #0a4f86;
        }
        [class^="peice"] label[for$="up"] {
            top: -6em;
            left: 3.5em;
        }
        [class^="peice"] label[for$="middle"] {
            display: none;
            z-index: 5;
            left: 3.5em;
        }
        [class^="peice"] label[for$="down"] {
            bottom: -6em;
            left: 3.5em;
        }
        [class^="peice"] label[for$="left"] {
            left: -6em;
            top: 3.5em;
        }
        [class^="peice"] label[for$="center"] {
            display: none;
            z-index: 5;
            top: 3.5em;
        }
        [class^="peice"] label[for$="right"] {
            right: -6em;
            top: 3.5em;
        }

        #a-up:checked ~ * [for="a-middle"],
        #b-up:checked ~ * [for="b-middle"],
        #c-up:checked ~ * [for="c-middle"],
        #d-up:checked ~ * [for="d-middle"],
        #e-up:checked ~ * [for="e-middle"],
        #f-up:checked ~ * [for="f-middle"],
        #g-up:checked ~ * [for="g-middle"],
        #h-up:checked ~ * [for="h-middle"] {
            display: block;
            transform: translate(0, 13em) rotate(45deg);
        }
        #a-down:checked ~ * [for="a-middle"],
        #b-down:checked ~ * [for="b-middle"],
        #c-down:checked ~ * [for="c-middle"],
        #d-down:checked ~ * [for="d-middle"],
        #e-down:checked ~ * [for="e-middle"],
        #f-down:checked ~ * [for="f-middle"],
        #g-down:checked ~ * [for="g-middle"],
        #h-down:checked ~ * [for="h-middle"] {
            display: block;
            transform: translate(0, -6em) rotate(45deg);
        }
        #a-left:checked ~ * [for="a-center"],
        #b-left:checked ~ * [for="b-center"],
        #c-left:checked ~ * [for="c-center"],
        #d-left:checked ~ * [for="d-center"],
        #e-left:checked ~ * [for="e-center"],
        #f-left:checked ~ * [for="f-center"],
        #g-left:checked ~ * [for="g-center"],
        #h-left:checked ~ * [for="h-center"] {
            display: block;
            transform: translate(13em, 0) rotate(45deg);
        }
        #a-right:checked ~ * [for="a-center"],
        #b-right:checked ~ * [for="b-center"],
        #c-right:checked ~ * [for="c-center"],
        #d-right:checked ~ * [for="d-center"],
        #e-right:checked ~ * [for="e-center"],
        #f-right:checked ~ * [for="f-center"],
        #g-right:checked ~ * [for="g-center"],
        #h-right:checked ~ * [for="h-center"] {
            display: block;
            transform: translate(-6em, 0) rotate(45deg);
        }

        #a-up:checked ~ * .peice-a,
        #b-up:checked ~ * .peice-b,
        #c-up:checked ~ * .peice-c,
        #d-up:checked ~ * .peice-d,
        #e-up:checked ~ * .peice-e,
        #f-up:checked ~ * .peice-f,
        #g-up:checked ~ * .peice-g,
        #h-up:checked ~ * .peice-h {
            --y: 0;
        }
        #a-middle:checked ~ * .peice-a,
        #b-middle:checked ~ * .peice-b,
        #c-middle:checked ~ * .peice-c,
        #d-middle:checked ~ * .peice-d,
        #e-middle:checked ~ * .peice-e,
        #f-middle:checked ~ * .peice-f,
        #g-middle:checked ~ * .peice-g,
        #h-middle:checked ~ * .peice-h {
            --y: 20em;
        }
        #a-down:checked ~ * .peice-a,
        #b-down:checked ~ * .peice-b,
        #c-down:checked ~ * .peice-c,
        #d-down:checked ~ * .peice-d,
        #e-down:checked ~ * .peice-e,
        #f-down:checked ~ * .peice-f,
        #g-down:checked ~ * .peice-g,
        #h-down:checked ~ * .peice-h {
            --y: 40em;
        }
        #a-left:checked ~ * .peice-a,
        #b-left:checked ~ * .peice-b,
        #c-left:checked ~ * .peice-c,
        #d-left:checked ~ * .peice-d,
        #e-left:checked ~ * .peice-e,
        #f-left:checked ~ * .peice-f,
        #g-left:checked ~ * .peice-g,
        #h-left:checked ~ * .peice-h {
            --x: 0;
        }
        #a-center:checked ~ * .peice-a,
        #b-center:checked ~ * .peice-b,
        #c-center:checked ~ * .peice-c,
        #d-center:checked ~ * .peice-d,
        #e-center:checked ~ * .peice-e,
        #f-center:checked ~ * .peice-f,
        #g-center:checked ~ * .peice-g,
        #h-center:checked ~ * .peice-h {
            --x: 20em;
        }
        #a-right:checked ~ * .peice-a,
        #b-right:checked ~ * .peice-b,
        #c-right:checked ~ * .peice-c,
        #d-right:checked ~ * .peice-d,
        #e-right:checked ~ * .peice-e,
        #f-right:checked ~ * .peice-f,
        #g-right:checked ~ * .peice-g,
        #h-right:checked ~ * .peice-h {
            --x: 40em;
        }
        .winner {
            font-family: arial;
            color: #fff;
            text-align: center;
            font-size: 4vw;
            z-index: 100;
            width: 100%;
            height: 2em;
            position: absolute;
            top: calc(50% - 1em);
            line-height: 2em;
            background: red;
            transform: scale(0);
        }
        #a-up:checked
            ~ #a-left:checked
            ~ #b-up:checked
            ~ #b-center:checked
            ~ #c-up:checked
            ~ #c-right:checked
            ~ #d-middle:checked
            ~ #d-left:checked
            ~ #e-middle:checked
            ~ #e-center:checked
            ~ #f-middle:checked
            ~ #f-right:checked
            ~ #g-down:checked
            ~ #g-left:checked
            ~ #h-down:checked
            ~ #h-center:checked
            ~ .winner {
            animation: winner 3s 1 1s;
        }
        @keyframes winner {
            0%,
            100% {
                transform: scale(0);
            }
            10%,
            90% {
                transform: scale(1);
            }
        }

        .selectBG {
            display: inline-block;
            font-family: arial;
            font-size: 2vmin;
            width: 8em;
            text-align: center;
            padding: 1em 0;
            background: #000;
            color: #fff;
            border: 1px solid #333;
            border-radius: 0.5em;
            margin: 2em 0.25em;
        }
        /* #cage:checked ~ *[for="cage"], */
        /* #cage:checked ~ * [class^="peice"].img {
            background-image: url(https://www.pupukkaltim.com/public/assets/files/img/about.jpg);
        } */
    </style>
    <script defer src="/scripts/alpine.min.js"></script>
    <script src="/scripts/tailwind.min.js"></script>
</head>

<body class="flex justify-center" style="background-color: #f7fafc; min-height: 100vh" x-data="{show: 1}">
    <div class="fixed top-0 left-0 right-0 bg-gray-100 text-gray-600 text-center flex items-center gap-1 justify-center py-2">
        <div id="online-led" class="w-2 h-2 rounded-full inline-block align-middle"></div>
        <p class="text-xs" id="online-indicator"></p>
    </div>

    <div class="mt-16">
        <div x-cloak x-show="show === 1" class="flex flex-col items-center justify-start text-center">
            <img src="/images/illustration/illustration_7.png" alt="Connection Lost" class="w-48 sm:w-96">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 my-6">
                Error 503: Service Unavailable
            </h1>
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                Application is temporarily maintenance.
            </h2>
            <p class="text-gray-600 mb-6">
                The application is currently unable to handle the request due to a temporary maintenance of the website.
            </p>

            <div class="mt-3">
                <small>
                    If you think this is an error, please let us know. <a href="#"
                        class="font-bold text-slate-900 hover:text-slate-700">Contact us</a>.
                </small>
            </div>
        </div>

        <div x-cloak x-show="show === 2" class="relative">
            <h1 class="text-center font-semibold text-gray-900 mb-2">
                Arrange the image to match the goals! <br>
                <button class="text-blue-700 text-sm text-center m-auto" id="change">change</button>
            </h1>

            <input type="radio" id="cage" name="image" checked />

            <input type="radio" id="a-up" name="a-vertical" />
            <input type="radio" id="a-middle" name="a-vertical" checked />
            <input type="radio" id="a-down" name="a-vertical" />
            <input type="radio" id="a-left" name="a-horazontal" checked />
            <input type="radio" id="a-center" name="a-horazontal" />
            <input type="radio" id="a-right" name="a-horazontal" />
            <input type="radio" id="b-up" name="b-vertical" checked />
            <input type="radio" id="b-middle" name="b-vertical" />
            <input type="radio" id="b-down" name="b-vertical" />
            <input type="radio" id="b-left" name="b-horazontal" checked />
            <input type="radio" id="b-center" name="b-horazontal" />
            <input type="radio" id="b-right" name="b-horazontal" />
            <input type="radio" id="c-up" name="c-vertical" />
            <input type="radio" id="c-middle" name="c-vertical" checked />
            <input type="radio" id="c-down" name="c-vertical" />
            <input type="radio" id="c-left" name="c-horazontal" />
            <input type="radio" id="c-center" name="c-horazontal" />
            <input type="radio" id="c-right" name="c-horazontal" checked />
            <input type="radio" id="d-up" name="d-vertical" checked />
            <input type="radio" id="d-middle" name="d-vertical" />
            <input type="radio" id="d-down" name="d-vertical" />
            <input type="radio" id="d-left" name="d-horazontal" />
            <input type="radio" id="d-center" name="d-horazontal" checked />
            <input type="radio" id="d-right" name="d-horazontal" />
            <input type="radio" id="e-up" name="e-vertical" />
            <input type="radio" id="e-middle" name="e-vertical" />
            <input type="radio" id="e-down" name="e-vertical" checked />
            <input type="radio" id="e-left" name="e-horazontal" />
            <input type="radio" id="e-center" name="e-horazontal" checked />
            <input type="radio" id="e-right" name="e-horazontal" />
            <input type="radio" id="f-up" name="f-vertical" />
            <input type="radio" id="f-middle" name="f-vertical" checked />
            <input type="radio" id="f-down" name="f-vertical" />
            <input type="radio" id="f-left" name="f-horazontal" />
            <input type="radio" id="f-center" name="f-horazontal" checked />
            <input type="radio" id="f-right" name="f-horazontal" />
            <input type="radio" id="g-up" name="g-vertical" checked />
            <input type="radio" id="g-middle" name="g-vertical" />
            <input type="radio" id="g-down" name="g-vertical" />
            <input type="radio" id="g-left" name="g-horazontal" />
            <input type="radio" id="g-center" name="g-horazontal" />
            <input type="radio" id="g-right" name="g-horazontal" checked />
            <input type="radio" id="h-up" name="h-vertical" />
            <input type="radio" id="h-middle" name="h-vertical" />
            <input type="radio" id="h-down" name="h-vertical" checked />
            <input type="radio" id="h-left" name="h-horazontal" checked />
            <input type="radio" id="h-center" name="h-horazontal" />
            <input type="radio" id="h-right" name="h-horazontal" />

            <div class="board">
                <div class="peice-a">
                    <label for="a-up"></label>
                    <label for="a-middle"></label>
                    <label for="a-down"></label>
                    <label for="a-left"></label>
                    <label for="a-center"></label>
                    <label for="a-right"></label>
                </div>
                <div class="peice-b">
                    <label for="b-up"></label>
                    <label for="b-middle"></label>
                    <label for="b-down"></label>
                    <label for="b-left"></label>
                    <label for="b-center"></label>
                    <label for="b-right"></label>
                </div>
                <div class="peice-c">
                    <label for="c-up"></label>
                    <label for="c-middle"></label>
                    <label for="c-down"></label>
                    <label for="c-left"></label>
                    <label for="c-center"></label>
                    <label for="c-right"></label>
                </div>
                <div class="peice-d">
                    <label for="d-up"></label>
                    <label for="d-middle"></label>
                    <label for="d-down"></label>
                    <label for="d-left"></label>
                    <label for="d-center"></label>
                    <label for="d-right"></label>
                </div>
                <div class="peice-e">
                    <label for="e-up"></label>
                    <label for="e-middle"></label>
                    <label for="e-down"></label>
                    <label for="e-left"></label>
                    <label for="e-center"></label>
                    <label for="e-right"></label>
                </div>
                <div class="peice-f">
                    <label for="f-up"></label>
                    <label for="f-middle"></label>
                    <label for="f-down"></label>
                    <label for="f-left"></label>
                    <label for="f-center"></label>
                    <label for="f-right"></label>
                </div>
                <div class="peice-g">
                    <label for="g-up"></label>
                    <label for="g-middle"></label>
                    <label for="g-down"></label>
                    <label for="g-left"></label>
                    <label for="g-center"></label>
                    <label for="g-right"></label>
                </div>
                <div class="peice-h">
                    <label for="h-up"></label>
                    <label for="h-middle"></label>
                    <label for="h-down"></label>
                    <label for="h-left"></label>
                    <label for="h-center"></label>
                    <label for="h-right"></label>
                </div>
                <div class="peice-a img"></div>
                <div class="peice-b img"></div>
                <div class="peice-c img"></div>
                <div class="peice-d img"></div>
                <div class="peice-e img"></div>
                <div class="peice-f img"></div>
                <div class="peice-g img"></div>
                <div class="peice-h img"></div>
            </div>

            <!-- image hint -->
            <div class="mt-2">
                <h1 class="text-center font-semibold text-gray-900">Your Goals</h1>
                <img src="https://www.pupukkaltim.com/public/assets/files/img/about.jpg"
                    alt="awdawd"
                    id="img-hint"
                    style="width: 100px; height: 100px; object-fit: cover; margin: auto; border-radius: 8px;" />
                <div class="winner">WINNER!</div>
            </div>
        </div>
    </div>


    <div class="flex flex-col fixed bottom-0 justify-center w-full">
        <div class="flex items-center gap-3 justify-center">
            <button
                class="text-blue-700 text-xs"
                @click="show === 1 ? show = 2 : show = 1"
                x-text="show === 1 ? 'Play game' : 'Back'">
                Play game
            </button>
        </div>


        <footer class="bg-gray-100 text-gray-600 text-center py-2">
            <p class="text-xs"><span x-text="new Date().getFullYear()"></span> &copy; PT. Pupuk Kalimantan Timur</p>
        </footer>
    </div>

    <div class="bg-[url('/images/pkt-pattern.png')] absolute bottom-0 w-full h-1/2 -z-10"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (navigator.onLine) {
                document.getElementById('online-led').style.backgroundColor = '#2ecc71';
                document.getElementById('online-indicator').textContent = 'You are online';
            } else {
                document.getElementById('online-led').style.backgroundColor = '#e74c3c';
                document.getElementById('online-indicator').textContent = 'You are offline';
            }

            setInterval(() => {
                if (navigator.onLine) {
                    document.getElementById('online-led').style.backgroundColor = '#2ecc71';
                    document.getElementById('online-indicator').textContent = 'You are online';
                } else {
                    document.getElementById('online-led').style.backgroundColor = '#e74c3c';
                    document.getElementById('online-indicator').textContent = 'You are offline';
                }
            }, 5000);
        });
    </script>

    <script>
        const radios = document.querySelectorAll('#cage:checked ~ * [class^="peice"].img');
        const imgHint = document.getElementById('img-hint');
        const randomize = () => {
            let images = [
                "/images/bg-login-pabrik.jpeg",
                "/images/bg-pabrik-1.jpeg",
            ];

            let random = Math.floor(Math.random() * images.length);
            return images[random];
        };

        const change = document.getElementById('change');
        change.addEventListener('click', () => {
            let img = randomize();
            radios.forEach((radio) => {
                radio.style.backgroundImage = `url(${img})`;
                imgHint.src = img;
            });
        });

        let img = randomize();
        radios.forEach((radio) => {
            radio.style.backgroundImage = `url(${img})`;
            imgHint.src = img;
        });
    </script>
</body>

</html>

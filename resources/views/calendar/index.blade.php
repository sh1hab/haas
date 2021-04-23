<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://unpkg.com/bootstrap-datepicker@1.8.0/dist/css/bootstrap-datepicker.standalone.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="">
<div class="container">
    <div class="row">
        <form method="post" action="/submit" class="form-inline" style="padding: 10px; margin: 10px">
            @csrf
            <div class="form-group mb-2">
                <div class="form-group">
                    <label>Year&nbsp;
                        <input id="datepicker" class="date-own form-control" name="year" style="width: 300px;"
                               type="text" value="@php echo $year @endphp" required>
                    </label>
                </div>
            </div>

            <div class="form-outline">
                <div class="form-group mx-sm-3 mb-2">
                    <label>Sign&nbsp;
                        <select name="sign" class="form-control" style="width: -moz-available !important;" required>
                            <option value="aries" {{ ($sign == 'aries' ? "selected":"") }}>Aries</option>
                            <option value="taurus" {{ ($sign == 'taurus' ? "selected":"") }} >Taurus</option>
                            <option value="gemini" {{ ($sign == 'gemini' ? "selected":"") }}>Gemini</option>
                            <option value="cancer" {{ ($sign == 'cancer' ? "selected":"") }}>Cancer</option>
                            <option value="leo" {{ ($sign == 'leo' ? "selected":"") }}>Leo</option>
                            <option value="virgo" {{ ($sign == 'virgo' ? "selected":"") }}>Virgo</option>
                            <option value="libra" {{ ($sign == 'libra' ? "selected":"") }}>Libra</option>
                            <option value="scorpio" {{ ($sign == 'scorpio' ? "selected":"") }}>Scorpio</option>
                            <option value="sagittarius" {{ ($sign == 'sagittarius' ? "selected":"") }}>Sagittarius
                            </option>
                            <option value="capricorn" {{ ($sign == 'capricorn' ? "selected":"") }}>Capricorn</option>
                            <option value="aquarius" {{ ($sign == 'aquarius' ? "selected":"") }}>Aquarius</option>
                            <option value="pisces" {{ ($sign == 'pisces' ? "selected":"") }}>Pisces</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="col-sm">
                <div class="">
                    <input type="submit" value="submit" class="btn btn-primary mb-2">
                </div>
            </div>
        </form>
        <br>
    </div>

    <div class="row">
        <p>
            Highest scored sign in Year {{$year}} is <i>{{ $highest_scored_sign}}</i>
        </p>
    </div>

    <div class="row">
        <p>
            Best month in Year {{$year}} is
            <i>{{ DateTime::createFromFormat('!m', $calendar_data['best_month'])->format('F') }}</i>
        </p>
    </div>
</div>

<div id="calendar"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script>
    let calendar = null;
    $(function () {
        $("#datepicker").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true,
        });

        // let currentYear = new Date().getFullYear();
        var currentYear = {!! $year !!}  ;
        let data = {!! $calendar_data !!};
        let months_score_details = JSON.parse(data.months_score_details);
        const color_codes = [
            '#FF0000',
            '#fe5900',
            '#Fe9000',
            '#faae00',
            '#FAE000',
            '#a6e000',
            '#79dd00',
            '#40dd00',
            '#25df00',
            '#00FF00'
        ]

        calendar = new Calendar('#calendar', {
            startYear: currentYear,
            enableContextMenu: true,
            enableRangeSelection: true,
            contextMenuItems: [],
            selectRange: function (e) {
                editEvent({startDate: e.startDate, endDate: e.endDate});
            },
            mouseOnDay: function (e) {
                if (e.events.length > 0) {
                    var content = '';

                    console.log(e.events);

                    for (var i in e.events) {
                        content += '<div class="event-tooltip-content">'
                            + '<div class="event-name" style="color:' + e.events[i].color + '">' + 'Score ' + e.events[i].score + '</div>'
                            // + '<div class="event-location">' + e.events[i].location + '</div>'
                            + '</div>';
                    }

                    $(e.element).popover({
                        trigger: 'manual',
                        container: 'body',
                        html: true,
                        content: content
                    });

                    $(e.element).popover('show');
                }
            },
            mouseOutDay: function (e) {
                if (e.events.length > 0) {
                    $(e.element).popover('hide');
                }
            },
            dayContextMenu: function (e) {
                $(e.element).popover('hide');
            }
        });
        let days = [];
        for (month = 1; month <= 12; month++) {
            for (day = 1; day <= months_score_details[month].length; day++) {
                days.push({
                    startDate: new Date(currentYear, month - 1, day),
                    endDate: new Date(currentYear, month - 1, day),
                    color: color_codes[months_score_details[month][day - 1] - 1],
                    score: months_score_details[month][day - 1]
                });
            }
        }

        calendar.setDataSource(days);
        $('.year-neighbor').hide();
        $('.year-neighbor2').hide();
        $('.next').hide();
        $('.prev').hide();


    });


</script>
</body>
</html>

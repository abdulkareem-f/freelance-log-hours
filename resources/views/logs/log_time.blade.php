@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        <h1>Log your hours</h1>
        <form action="process_form.php" method="POST">
            <!-- Start Time Field -->
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
            </div>

            <!-- End Time Field -->
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <hr>
        <h1>Or you can start / stop timer</h1>
        <hr>

        <div class="container mt-5">
            <h1>Timer Control</h1>
            <div class="row">
                <div class="col-md-6">
                    <!-- Start Timer Form -->
                    <form id="startTimerForm">
                        <button type="button" class="btn btn-primary" onclick="startTimer()">Start Timer</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <!-- Stop Timer Form -->
                    <form id="stopTimerForm">
                        <button type="button" class="btn btn-danger" onclick="stopTimer()">Stop Timer</button>
                    </form>
                </div>
            </div>
            <!-- Timer Display -->
            <div class="mt-3">
                <h3>Timer: <span id="timerDisplay">00:00:00</span></h3>
            </div>
        </div>

    </div>

@endsection

@section('js')
<script>
    let timer;
    let seconds = 0, minutes = 0, hours = 0;
    const timerDisplay = document.getElementById('timerDisplay');

    function startTimer() {
        if (!timer) {
            timer = setInterval(updateTimer, 1000);
        }
    }

    function stopTimer() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    function updateTimer() {
        seconds++;
        if (seconds === 60) {
            seconds = 0;
            minutes++;
            if (minutes === 60) {
                minutes = 0;
                hours++;
            }
        }

        const formattedTime = pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
        timerDisplay.textContent = formattedTime;
    }

    function pad(num) {
        return num.toString().padStart(2, '0');
    }
</script>
@endsection

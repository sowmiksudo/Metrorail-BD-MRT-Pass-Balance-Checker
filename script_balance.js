document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.modal form');
    const resultContainer = document.querySelector('.modal .result ul');
    let dayOfWeek = new Date().getDay();

    // dayOfWeek = 5;

    const currentDateTimeContainer = document.querySelector('.modal .date-time-container');

    // Function to update the current date and time
    function updateDateTime() {
        const now = new Date();
        const formattedTime = now.toLocaleString('en-US', {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: true
        });
        const formattedDate = now.toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        currentDateTimeContainer.innerHTML = `<div class="current-time">Time: ${formattedTime}</div><div class="current-date">${formattedDate}</div>`;
    }

    // Update the current date and time when the page loads   
    updateDateTime();

    // Update the current date and time every second
    setInterval(updateDateTime, 1000);

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        card = document.getElementById('card_num').value;
        fetch('https://apis.neopandora.com/metrorail/info.php?card='+card, {
            method: 'GET'
        })
            .then(function (response) { return response.json(); })
            .then(function (json) {
                console.log(json);
                document.getElementById("name").innerText = json.Name;
                document.getElementById("id").innerText = json.ID;
                document.getElementById("balance").innerText = json.Balance;
                document.getElementById("status").innerText = json.Status;
                document.getElementById("result").classList.remove('d-none');
            });
    });

    if (dayOfWeek === 5) {
        const textContainer = document.querySelector('.modal .body .date-time-wrapper .warning .reminder');
        textContainer.innerHTML += '<strong style="color: red;">Reminder:</strong> Metro Rail is off on Fridays';
    }
});


const resetButton = document.querySelector('.modal form .resetbtn');
resetButton.addEventListener('click', function () {
    // Reload the page
    location.reload();
});

const closeButton = document.querySelector('.modal .header .btn-close');
closeButton.addEventListener('click', function () {
    // Reload the page
    location.reload();
});

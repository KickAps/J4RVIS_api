import {hideLoader, showLoader} from "./app";

function getDates(startDate, stopDate) {
    let dateArray = [];
    let currentDate = startDate;
    while(currentDate <= stopDate) {
        dateArray.push(formatDate(currentDate));
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return dateArray;
}

function formatDate(date) {
    let d = new Date(date);
    let month = d.getMonth() + 1;
    let day = d.getDate();
    let year = d.getFullYear();

    if(month < 10)
        month = '0' + month;
    if(day < 10)
        day = '0' + day;

    return [year, month, day].join('-');
}

function callGarminConnect(dateArray, garmin_connect_sleep_data_url, activity_sleep_url) {
    let day = dateArray[0];
    console.log(day);
    dateArray.shift();
    fetch(garmin_connect_sleep_data_url + "?day=" + day).then(function(response) {
        return response.json();
    }).then(function(data) {
        console.log(data);
        if(data['error']) {
            console.log(data['error']);
            document.querySelector("a#warning_cookies").removeAttribute('hidden');
            hideLoader();
            return;
        } else if(data['startedAt'] !== 0) {
            callStartActivity(dateArray, data, activity_sleep_url);
        } else {
            console.log("No sleep data");
        }
        if(dateArray.length !== 0) {
            callGarminConnect(dateArray, garmin_connect_sleep_data_url, activity_sleep_url);
        }
    }).catch(function(error) {
        console.log(error);
    });
}

function callStartActivity(dateArray, data, activity_sleep_url) {
    let options = {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    fetch(activity_sleep_url, options).then(function(response) {
        return response;
    }).then(function(data) {
        console.log(data);
        if(dateArray.length === 0) {
            updateLastDataSleepRefresh();
            hideLoader();
            location.reload();
        }
    }).catch(function(error) {
        console.log(error);
    });
}

function updateLastDataSleepRefresh() {
    let date = new Date();
    date.setHours(0, 0, 0, 0);

    let data = {
        timestamp: date.getTime() / 1000
    }
    let options = {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    let url = document.querySelector("input#garmin_connect_update_last_data_sleep_refresh_url").value;
    fetch(url, options).then(function(response) {
        return response;
    }).then(function(data) {
        console.log(data);
    }).catch(function(error) {
        console.log(error);
    });
}

document.addEventListener('DOMContentLoaded', function(e) {
    // Refresh Button
    let button_refresh_sleep_data = document.querySelector("a#button_refresh_sleep_data");
    button_refresh_sleep_data.addEventListener("click", function(e) {
        showLoader();
        // URL
        let garmin_connect_sleep_data_url = document.querySelector("input#garmin_connect_sleep_data_url").value;
        let activity_sleep_url = document.querySelector("input#activity_sleep_url").value;

        let last_data_sleep_refresh = document.querySelector("input#last_data_sleep_refresh").value;

        let dateArray = getDates(new Date(last_data_sleep_refresh * 1000), new Date());

        callGarminConnect(dateArray, garmin_connect_sleep_data_url, activity_sleep_url);
    });
});
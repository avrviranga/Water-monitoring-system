function updateCounter(elementId, targetValue) {
    let element = document.getElementById(elementId);
    let circleElement = document.querySelector(`.circle${elementId.slice(-1)}`);
    
    console.log(`Updating ${elementId} with value ${targetValue}`);
    console.log('Element:', element);
    console.log('Circle Element:', circleElement);

    if (element && circleElement) {
        let circumference = 2 * Math.PI * 90; // 90 is the radius of the circle

        element.innerHTML = targetValue.toFixed(1) + "%";
        let offset = circumference - (targetValue / 100 * circumference);
        circleElement.style.strokeDashoffset = offset;
        console.log(`Updated ${elementId} successfully`);
    } else {
        console.error(`Failed to update ${elementId}. Element or circle not found.`);
    }
}

function fetchAndUpdateSensorData() {
    fetch('get_sensor_data.php')
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data);
            updateCounter("number1", data.Distance_Percentage);
            updateCounter("number2", data.Turbid_Percentage);
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    fetchAndUpdateSensorData();
    setInterval(fetchAndUpdateSensorData, 5000); // Update every 5 seconds
});

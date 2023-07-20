function calculateTimeAgo(timestamp) {
    var postDate = new Date(timestamp);
    var currentDate = new Date();
    var timeDifference = Math.abs(currentDate - postDate);

    var seconds = Math.floor(timeDifference / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);

    if (days > 1) {
        return days + " dni temu";
    } else if (days === 1) {
        return days + " dzień temu";
    } else if (hours === 1) {
        return hours + " godzinę temu";
    } else if (hours > 1) {
        return hours + " godzin(y) temu";
    } else if (minutes > 1) {
        return minutes + " minut(y) temu";
    } else {
        return "Teraz";
    }
}
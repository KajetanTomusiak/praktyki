function confirmDelete(taskId) {
    if (confirm("Czy na pewno chcesz usunąć to zadanie?")) {
        window.location.href = "delete.php?id=" + taskId;
    }
}
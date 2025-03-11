document.addEventListener('DOMContentLoaded', function() {
    const employeeLinks = document.querySelectorAll('.employee-link');
    const employeeCard = document.getElementById('employee-card');
    const employeeName = document.getElementById('employee-name');
    const employeeRole = document.getElementById('employee-role');
    const employeeEmail = document.getElementById('employee-email');

    employeeLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            employeeName.textContent = this.getAttribute('data-name');
            employeeRole.textContent = this.getAttribute('data-role');
            employeeEmail.textContent = this.getAttribute('data-email');
            employeeCard.style.display = 'block';
        });
    });
});
<div class="modal fade" id="repeatInfoModal" tabindex="-1" aria-labelledby="repeatInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="repeatInfoModalLabel">How Repeat Works</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Choose how often this task should repeat:</p>
                <ul>
                <li>
                    <strong>No Repeat</strong>: Task occurs only once.</li>
                <li>
                    <strong>Daily</strong>: Task will repeat every day. A new task is created only when the <strong>current date reaches the End Date</strong> of the previous task.</li>
                <li>
                    <strong>Weekly</strong>: Task repeats every week. A new task is generated after the End Date of the last task.</li>
                <li>
                    <strong>Monthly</strong>: Task repeats every month. A new task is generated after the End Date of the last task.</li>
                </ul>
                <p><strong>Repeat Count</strong> determines how many additional tasks will be generated.</p>
                <p><strong>Example:</strong></p>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Original</td><td>06/10/2025</td><td>07/10/2025</td></tr>
                        <tr><td>1st Repeat</td><td>07/10/2025</td><td>07/10/2025</td></tr>
                        <tr><td>2nd Repeat</td><td>08/10/2025</td><td>08/10/2025</td></tr>
                        <tr><td>3rd Repeat</td><td>09/10/2025</td><td>09/10/2025</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
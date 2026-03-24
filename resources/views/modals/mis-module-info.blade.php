<div class="modal fade" id="misInfoModal" tabindex="-1" aria-labelledby="misInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="misInfoModalLabel">
                    <i class="fas fa-info-circle me-2"></i> About the MIS Module
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">Overview</h6>
                <p>The MIS (Management Information System) module helps teams set, track, and analyze weekly performance targets for various MIS points. It provides a structured way to monitor progress through weekly targets, daily achieved values, and summary reports.</p>
                <h6 class="fw-bold mt-3">Key Features</h6>
                <ul>
                    <li>
                        <strong>Setting Weekly Targets:</strong> Admins and team managers can set weekly targets for specific teams and MIS points (e.g., sales, leads) via the "Add Weekly Target" button. Select a team, year, week, task, and target value in the modal.
                    </li>
                    <li>
                        <strong>Updating Achieved Values:</strong> Admins can update daily achieved values for each MIS point by clicking the "Achieved" input field, which opens a modal to enter daily data for a specific week.
                    </li>
                    <li>
                        <strong>Auto-Assign Targets:</strong> Enable the "Auto-Assign Targets" option when setting targets to automatically carry forward targets to the next week when the current week ends.
                    </li>
                    <li><strong>Viewing Data:</strong>
                        <ul>
                            <li>
                                <strong>Weekly Targets View:</strong> Displays targets, achieved values, and achievement percentages for each MIS point across weeks. Filter by team and year.
                            </li>
                            <li>
                                <strong>Summary Report:</strong> Shows aggregated targets, achieved values, percentages, and variances for selected teams, years, months, or weeks.
                            </li>
                            <li>
                                <strong>Daily Report:</strong> Provides detailed daily entries for teams, showing achieved values per MIS point for specific dates or weeks.
                            </li>
                        </ul>
                    </li>
                    <li><strong>Percentage Calculations:</strong>
                        <ul>
                            <li>
                                <strong>Percentage (%):</strong> Calculated as (Achieved / Target) * 100 for each MIS point and week.
                            </li>
                            <li>
                                <strong>Normalized Percentage:</strong> Represents the achieved value as a percentage of the total target sum for all MIS points in a week, useful for comparing task contributions.
                            </li>
                        </ul>
                    </li>
                </ul>

                <h6 class="fw-bold mt-3">How to Use</h6>
                <ol>
                    <li>
                        <strong>Set Targets:</strong> Click "Add Weekly Target," select a team, year, week, and enter tasks and targets. Enable auto-assign if needed.
                    </li>
                    <li>
                        <strong>Update Achievements:</strong> Admins can edit achieved values directly in the table (if a team is selected) or via the daily data modal by clicking an "Achieved" field.
                    </li>
                    <li>
                        <strong>Filter Data:</strong> Use the team and year filters to view specific data. In the summary report, filter by month or week for more granularity.
                    </li>
                    <li>
                        <strong>Review Reports:</strong> Access the summary report for overall performance or the daily report for detailed daily breakdowns.
                    </li>
                    <li>
                        <strong>Export Data:</strong> In the summary report, use the "Export to Excel" button to download data as a CSV file.
                    </li>
                </ol>
                <h6 class="fw-bold mt-3">Notes</h6>
                <ul>
                    <li>Only admins and team managers can edit targets and achieved values when a specific team is selected.</li>
                    <li>Ensure the selected date for daily entries aligns with the chosen week to maintain data consistency.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

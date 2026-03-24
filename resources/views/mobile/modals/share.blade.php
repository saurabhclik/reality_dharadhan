<div id="shareModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 20px; border-radius: 10px; width: 90%; max-width: 400px;">
        <h3 style="margin-top: 0;">Transfer Leads</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Share with:</label>
            <select id="shareUser" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">Select User</option>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="cancelShare" style="flex: 1; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">Cancel</button>
            <button id="confirmShare" style="flex: 1; padding: 10px; background: #CF5D3B; color: white; border: none; border-radius: 5px;">Share</button>
        </div>
    </div>
</div>
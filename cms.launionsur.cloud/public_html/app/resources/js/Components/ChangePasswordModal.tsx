import { useState } from 'react';

export default function ChangePasswordModal({ onClose }: { onClose?: () => void }) {
    const [current, setCurrent] = useState('');
    const [newPass, setNewPass] = useState('');
    const [confirm, setConfirm] = useState('');

    const submit = async () => {
        try {
            await window.axios.post('/api/users/' + window.Laravel?.user?.id + '/change-password', {
                current_password: current,
                new_password: newPass,
                new_password_confirmation: confirm,
            });
            onClose && onClose();
            alert('Password changed');
        } catch (e) {
            alert('Error changing password');
        }
    };

    return (
        <div className="p-4">
            <h3 className="mb-4 text-lg font-semibold">Change Password</h3>
            <div className="mb-2">
                <label>Current password</label>
                <input
                    type="password"
                    value={current}
                    onChange={e => setCurrent(e.target.value)}
                    className="mt-1 block w-full"
                />
            </div>
            <div className="mb-2">
                <label>New password</label>
                <input
                    type="password"
                    value={newPass}
                    onChange={e => setNewPass(e.target.value)}
                    className="mt-1 block w-full"
                />
            </div>
            <div className="mb-2">
                <label>Confirm password</label>
                <input
                    type="password"
                    value={confirm}
                    onChange={e => setConfirm(e.target.value)}
                    className="mt-1 block w-full"
                />
            </div>
            <div className="mt-4 flex justify-end">
                <button className="btn btn-secondary me-2" onClick={() => onClose && onClose()}>
                    Cancel
                </button>
                <button className="btn btn-primary" onClick={submit}>
                    Change
                </button>
            </div>
        </div>
    );
}

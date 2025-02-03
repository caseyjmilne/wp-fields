import React, { useState } from 'react';

function FieldItem({ field, onUpdate, onDelete }) {
    const [isEditing, setIsEditing] = useState(false);
    const [editedField, setEditedField] = useState({ ...field });

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setEditedField(prev => ({ ...prev, [name]: value }));
    };

    // Update the field
    const handleSave = async () => {
        try {
            const response = await fetch(`/wp-json/wp-fields/v1/fields/${field.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.wpApiSettings.nonce,
                  },
                body: JSON.stringify({
                    name: editedField.name,
                    type: editedField.type,
                    position: parseInt(editedField.position, 10)
                })
            });

            if (response.ok) {
                onUpdate(editedField);
                setIsEditing(false);
            } else {
                alert('Failed to update field.');
            }
        } catch (error) {
            console.error('Error updating field:', error);
        }
    };

    // Delete the field
    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this field?')) {
            try {
                const response = await fetch(`/wp-json/wp-fields/v1/fields/${field.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.wpApiSettings.nonce,
                    },
                });

                if (response.ok) {
                    onDelete(field.id);
                } else {
                    alert('Failed to delete field.');
                }
            } catch (error) {
                console.error('Error deleting field:', error);
            }
        }
    };

    return (
        <div style={{ marginBottom: '10px', border: '1px solid #ccc', padding: '10px' }}>
            {isEditing ? (
                <div>
                    <input
                        type="text"
                        name="name"
                        value={editedField.name}
                        onChange={handleInputChange}
                    />
                    <input
                        type="text"
                        name="type"
                        value={editedField.type}
                        onChange={handleInputChange}
                    />
                    <input
                        type="number"
                        name="position"
                        value={editedField.position}
                        onChange={handleInputChange}
                    />
                    <button onClick={handleSave}>Save</button>
                    <button onClick={() => setIsEditing(false)}>Cancel</button>
                </div>
            ) : (
                <div>
                    <strong>{field.name}</strong> ({field.type}) - Position: {field.position}
                    <button onClick={() => setIsEditing(true)}>Edit</button>
                    <button onClick={handleDelete}>Delete</button>
                </div>
            )}
        </div>
    );
}

export default FieldItem;

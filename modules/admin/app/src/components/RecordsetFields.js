import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import FieldItem from './FieldItem';

function RecordsetFields() {
    const { id } = useParams();
    const [fields, setFields] = useState([]);
    const [newField, setNewField] = useState({ name: '', type: '', position: 0 });

    // Fetch existing fields
    useEffect(() => {
        fetch(`/wp-json/wp-fields/v1/fields?recordset_id=${id}`)
            .then(response => response.json())
            .then(data => setFields(data))
            .catch(error => console.error('Error fetching fields:', error));
    }, [id]);

    // Handle input changes
    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setNewField(prev => ({ ...prev, [name]: value }));
    };

    // Add new field
    const handleAddField = async () => {
        if (!newField.name || !newField.type) {
            alert('Name and Type are required.');
            return;
        }

        try {
            const response = await fetch('/wp-json/wp-fields/v1/fields', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.wpApiSettings.nonce,
                },
                body: JSON.stringify({
                    recordset_id: id,
                    name: newField.name,
                    type: newField.type,
                    position: parseInt(newField.position, 10) || 0
                })
            });

            const data = await response.json();
            if (response.ok) {
                setFields([...fields, { ...newField, id: data.id }]);
                setNewField({ name: '', type: '', position: 0 });
            } else {
                alert(`Error: ${data.message}`);
            }
        } catch (error) {
            console.error('Error adding field:', error);
        }
    };

    // Handle field updates
    const handleUpdateField = (updatedField) => {
        setFields(fields.map(field => (field.id === updatedField.id ? updatedField : field)));
    };

    // Handle field deletion
    const handleDeleteField = (deletedFieldId) => {
        setFields(fields.filter(field => field.id !== deletedFieldId));
    };

    return (
        <div>
            <h2>Manage Fields for Recordset ID: {id}</h2>

            <div style={{ marginBottom: '20px' }}>
                <h3>Add New Field</h3>
                <input
                    type="text"
                    name="name"
                    placeholder="Field Name"
                    value={newField.name}
                    onChange={handleInputChange}
                />
                <input
                    type="text"
                    name="type"
                    placeholder="Field Type"
                    value={newField.type}
                    onChange={handleInputChange}
                />
                <input
                    type="number"
                    name="position"
                    placeholder="Position"
                    value={newField.position}
                    onChange={handleInputChange}
                />
                <button onClick={handleAddField}>Add Field</button>
            </div>

            <h3>Existing Fields</h3>
            {fields.map(field => (
                <FieldItem
                    key={field.id}
                    field={field}
                    onUpdate={handleUpdateField}
                    onDelete={handleDeleteField}
                />
            ))}
        </div>
    );
}

export default RecordsetFields;

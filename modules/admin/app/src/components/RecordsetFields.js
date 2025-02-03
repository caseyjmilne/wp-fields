import React from 'react';
import { useParams } from 'react-router-dom';

function RecordsetFields() {
    const { id } = useParams();

    const handleAddField = () => {
        alert(`Add Field for Recordset ID: ${id}`);
    };

    return (
        <div>
            <h2>Manage Fields for Recordset ID: {recordsetId}</h2>
            <button onClick={handleAddField}>Add Field</button>
        </div>
    );
}

export default RecordsetFields;

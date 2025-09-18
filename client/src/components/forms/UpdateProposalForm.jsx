"use client"

import { useState } from "react"
import { useFormStatus } from "react-dom"
import { updateProposalAction } from "@/actions/proposalActions"
import styles from "./UpdateProposalForm.module.css"

function SubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={`${styles.submitButton} ${pending ? styles.loading : ""}`}>
      {pending ? "Updating..." : "Update Proposal"}
    </button>
  )
}

export default function UpdateProposalForm({ proposal, onSuccess, onCancel }) {
  const { pending } = useFormStatus();
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")
  const [docs, setDocs] = useState([{ id: Date.now(), file: null }]);

  const handleAddImage = () => {
    setDocs([...docs, { id: Date.now(), file: null }]);
  };

  const handleRemoveImage = (id) => {
    if (docs.length > 1) {
      setDocs(docs.filter(img => img.id !== id));
    }
  };

  const handleFileChange = (e, id) => {
    const file = e.target.files[0];
    const newImages = docs.map(img => {
      if (img.id === id) {
        return { ...img, file: file };
      }
      return img;
    });
    setDocs(newImages);
  };

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccessMessage("")

    const docs = formData.getAll("docs[]")
    formData.delete("docs[]")

    docs.forEach((file) => {
      if (file instanceof File && file.size > 0) {
        formData.append("docs[]", file);
      } else {
        console.log("File is empty or not a File object.");
      }
    });

    console.log("Inspecting FormData content:");
    for (const [key, value] of formData.entries()) {
      // You'll see the file object here, not just an empty object
      console.log(`${key}: ${value}`);
    }

    try {
      const result = await updateProposalAction(proposal.id, formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccessMessage(result.success)
        setTimeout(() => {
          onSuccess()
        }, 1000)
      }
    } catch (error) {
      setErrors({ error: "An unexpected error occurred" })
    }
  }

  return (
    <form action={handleSubmit} className={styles.form}>
      {successMessage && <div className={styles.success}>{successMessage}</div>}

      {errors.error && (
        <div className={styles.error}>
          {typeof errors.error === "object" ? JSON.stringify(errors.error) : errors.error}
        </div>
      )}

      <div className={styles.field}>
        <label htmlFor="title" className={styles.label}>
          Title
        </label>
        <input type="text" id="title" name="title" defaultValue={proposal.title} className={styles.input} />
        {errors.title && <div className={styles.fieldError}>{errors.title}</div>}
      </div>

      <div className={styles.field}>
        <label htmlFor="description" className={styles.label}>
          Description
        </label>
        <textarea
          id="description"
          name="description"
          rows={4}
          defaultValue={proposal.description}
          className={styles.textarea}
        />
        {errors.description && <div className={styles.fieldError}>{errors.description}</div>}
      </div>

      <div className={styles.formGroup}>
        <div className={styles.groupHeader}>
          <label className={styles.label}>Proposal PDF Files</label>
          <span className={styles.warning}>Warning: Previous Uploaded files will be replaced</span>
        </div>
        {docs.map((input, index) => (
          <div key={input.id} className={styles.dynamicField}>
            <div className={styles.fileUpload}>
              <label htmlFor={`pdf-${input.id}`} className={styles.fileButton}>Choose File</label>
              <span className={styles.fileName}>{input.file ? input.file.name : 'No file chosen'}</span>
              <input
                type="file"
                id={`pdf-${input.id}`}
                name="docs[]"
                accept=".pdf"
                onChange={(e) => handleFileChange(e, input.id)}
                className={styles.hiddenInput}
                disabled={pending}
              />
            </div>
            {docs.length > 1 && (
              <button type="button" onClick={() => handleRemoveImage(input.id)} className={styles.removeButton} disabled={pending}>-</button>
            )}
            {index === docs.length - 1 && (
              <button type="button" onClick={handleAddImage} className={styles.addButton} disabled={pending}>+</button>
            )}
          </div>
        ))}
        {errors.docs && <div className={styles.fieldError}>{errors.docs}</div>}
      </div>

      <div className={styles.actions}>
        <button type="button" onClick={onCancel} className={styles.cancelButton}>
          Cancel
        </button>
        <SubmitButton />
      </div>
    </form>
  )
}

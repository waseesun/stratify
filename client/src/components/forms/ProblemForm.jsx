"use client"

import { useFormStatus } from "react-dom"
import {SubmitButton} from "@/components/buttons/Buttons"
import styles from "./ProblemForm.module.css"
import { useEffect, useState } from "react"
import { getCategoriesAction } from "@/actions/categoryActions";
import { getUserIdAction } from "@/actions/authActions"

function FormContent({ initialData = {} }) {
  const { pending } = useFormStatus()
  const [categories, setCategories] = useState([])
  
  const fetchCategories = async () => {
    setCategories([])

    try {
      const result = await getCategoriesAction()

      if (result.error) {
        console.error("Error fetching categories:", result.error)
      } else {
        setCategories(result.data)
      }
    } catch (error) {
      console.error("Error fetching categories:", error)
    }
  }
  
  useEffect(() => {
    fetchCategories()
  }, [])

  return (
    <>
      <div className={styles.formGroup}>
        <label htmlFor="title" className={styles.label}>
          Title *
        </label>
        <input
          type="text"
          id="title"
          name="title"
          required
          className={styles.input}
          defaultValue={initialData.title || ""}
          disabled={pending}
        />
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="description" className={styles.label}>
          Description *
        </label>
        <textarea
          id="description"
          name="description"
          required
          rows={4}
          className={styles.textarea}
          defaultValue={initialData.description || ""}
          disabled={pending}
        />
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="category" className={styles.label}>Category</label>
        <select id="category" name="category" className={styles.select}>
          <option value="">All Categories</option>
          {categories.map((cat) => (
            <option key={cat.id} value={cat.id}>
              {cat.name}
            </option>
          ))}
        </select>
      </div>


      <div className={styles.formRow}>
        <div className={styles.formGroup}>
          <label htmlFor="budget" className={styles.label}>
            Budget *
          </label>
          <input
            type="number"
            id="budget"
            name="budget"
            required
            min="0"
            step="0.01"
            className={styles.input}
            defaultValue={initialData.budget || ""}
            disabled={pending}
          />
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="timeline_value" className={styles.label}>
            Timeline Value *
          </label>
          <input
            type="number"
            id="timeline_value"
            name="timeline_value"
            required
            min="1"
            className={styles.input}
            defaultValue={initialData.timeline_value || ""}
            disabled={pending}
          />
        </div>
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="timeline_unit" className={styles.label}>
          Timeline Unit *
        </label>
        <select
          id="timeline_unit"
          name="timeline_unit"
          required
          className={styles.select}
          defaultValue={initialData.timeline_unit || "0"}
          disabled={pending}
        >
          <option value="day">Days</option>
          <option value="week">Weeks</option>
          <option value="month">Months</option>
          <option value="year">Years</option>
        </select>
      </div>

      {initialData.status !== undefined && (
        <div className={styles.formGroup}>
          <label htmlFor="status" className={styles.label}>
            Status
          </label>
          <select
            id="status"
            name="status"
            className={styles.select}
            defaultValue={initialData.status || "open"}
            disabled={pending}
          >
            <option value="open">Open</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      )}

      <div className={styles.formGroup}>
        <label htmlFor="skills" className={styles.label}>
          Skills
        </label>
        <input
          type="text"
          id="skills"
          name="skills"
          className={styles.input}
          placeholder="Comma-separated skills"
          defaultValue={initialData.skills || ""}
          disabled={pending}
        />
      </div>

      <div className={styles.buttonGroup}>
        <SubmitButton />
      </div>
    </>
  )
}

export default function ProblemForm({ onSubmit, initialData }) {
  const [userId, setUserId] = useState(null);

  const fetchUserID = async () => {
    const userId = await getUserIdAction();
    console.log(userId)

    if (userId) {
      setUserId(userId);
    }
  }

  useEffect(() => {
    fetchUserID();
  }, [])

  const handleSubmit = async (formData) => {
    const skillsString = formData.get("skills");
    const skillsArray = skillsString ? skillsString.split(",").map(skill => skill.trim()) : [];
    
    // Remove the original skills string and append the new array
    formData.delete("skills");
    skillsArray.forEach(skill => {
        formData.append("skills[]", skill); // Use "[]" to signify an array in FormData
    });

    formData.append("company", userId);
    await onSubmit(formData)
  }

  return (
    <form action={handleSubmit} className={styles.form}>
      <FormContent initialData={initialData} />
    </form>
  )
}

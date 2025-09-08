"use client"

import { useEffect, useState } from "react"
import { getCategoriesAction } from "@/actions/categoryActions"
import { updateUserCategoriesAction } from "@/actions/userActions"
import { UpdateButton } from "@/components/buttons/Buttons"
import styles from "./CategoryForm.module.css"

export default function CategoryForm({ userId, initialCategoryNames }) {
  const [availableCategories, setAvailableCategories] = useState([])
  const [selectedCategoryNames, setSelectedCategoryNames] = useState(new Set())
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")

  useEffect(() => {
    const fetchCategories = async () => {
      const result = await getCategoriesAction()
      if (result.error) {
        setErrors(result.error)
      } else {
        setAvailableCategories(result.data)
        
        if (initialCategoryNames) {
          setSelectedCategoryNames(new Set(initialCategoryNames))
        }
      }
    }
    fetchCategories()
  }, [initialCategoryNames])

  const handleCheckboxChange = (categoryName) => {
    setSelectedCategoryNames(prev => {
      const newSet = new Set(prev)
      if (newSet.has(categoryName)) {
        newSet.delete(categoryName)
      } else {
        newSet.add(categoryName)
      }
      return newSet
    })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccess("")

    const selectedCategoryIds = availableCategories
      .filter(category => selectedCategoryNames.has(category.name))
      .map(category => category.id)
    
    try {
      const result = await updateUserCategoriesAction(userId, {
        categories: selectedCategoryIds,
      })
      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccess(result.success)
      }
    } catch (error) {
      setErrors({ general: "An unexpected error occurred" })
    }
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Update User Categories</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      <form onSubmit={handleSubmit} className={styles.form}>
        <div className={styles.categoriesGrid}>
          {availableCategories.map((category) => (
            <div key={category.id} className={styles.checkboxGroup}>
              <input
                type="checkbox"
                id={`cat-${category.id}`}
                name="categories"
                value={category.id}
                className={styles.checkbox}
                checked={selectedCategoryNames.has(category.name)}
                onChange={() => handleCheckboxChange(category.name)}
              />
              <label htmlFor={`cat-${category.id}`} className={styles.checkboxLabel}>
                {category.name}
              </label>
            </div>
          ))}
        </div>

        <UpdateButton>Update Categories</UpdateButton>
      </form>
    </div>
  )
}
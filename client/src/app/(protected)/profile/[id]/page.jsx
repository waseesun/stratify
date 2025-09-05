"use client"

import { useState, useEffect } from "react"
import { getUserIdAction } from "@/actions/authActions"
import ProfileForm from "@/components/forms/ProfileForm"
import PortfolioForm from "@/components/forms/PortfolioForm"
import CategoryForm from "@/components/forms/CategoryForm"
import DeleteModal from "@/components/modals/DeleteModal"
import styles from "./page.module.css"

export default function ProfilePage() {
  const [userId, setUserId] = useState(null)
  const [activeForm, setActiveForm] = useState("profile")
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchUserId = async () => {
      try {
        const result = await getUserIdAction()
        if (result) {
          setUserId(result)
        }
      } catch (error) {
        console.error("Failed to fetch user ID:", error)
      } finally {
        setLoading(false)
      }
    }

    fetchUserId()
  }, [])

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading...</div>
      </div>
    )
  }

  if (!userId) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Failed to load user profile</div>
      </div>
    )
  }

  const renderActiveForm = () => {
    switch (activeForm) {
      case "profile":
        return <ProfileForm userId={userId} />
      case "portfolio":
        return <PortfolioForm userId={userId} />
      case "category":
        return <CategoryForm userId={userId} />
      default:
        return <ProfileForm userId={userId} />
    }
  }

  return (
    <div className={styles.container}>
      <div className={styles.content}>
        <div className={styles.header}>
          <h1 className={styles.title}>Profile Settings</h1>
        </div>

        <div className={styles.navigation}>
          <button
            className={`${styles.navButton} ${activeForm === "profile" ? styles.active : ""}`}
            onClick={() => setActiveForm("profile")}
          >
            Update Profile
          </button>
          <button
            className={`${styles.navButton} ${activeForm === "portfolio" ? styles.active : ""}`}
            onClick={() => setActiveForm("portfolio")}
          >
            Portfolio Links
          </button>
          <button
            className={`${styles.navButton} ${activeForm === "category" ? styles.active : ""}`}
            onClick={() => setActiveForm("category")}
          >
            Categories
          </button>
          <button className={styles.deleteButton} onClick={() => setShowDeleteModal(true)}>
            Delete Account
          </button>
        </div>

        <div className={styles.formContainer}>{renderActiveForm()}</div>

        {showDeleteModal && <DeleteModal userId={userId} onClose={() => setShowDeleteModal(false)} />}
      </div>
    </div>
  )
}

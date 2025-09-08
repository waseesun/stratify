"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import { getUserRoleAction } from "@/actions/authActions"
import { getUserAction } from "@/actions/userActions"
import ProfileForm from "@/components/forms/ProfileForm"
import PortfolioForm from "@/components/forms/PortfolioForm"
import CategoryForm from "@/components/forms/CategoryForm"
import DeleteModal from "@/components/modals/DeleteModal"
import styles from "./page.module.css"

export default function SettingsPage() {
  const params = useParams()
  const [userRole, setUserRole] = useState(null)
  const [userProfile, setUserProfile] = useState(null) // State to hold full user profile
  const [activeForm, setActiveForm] = useState("profile")
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [loading, setLoading] = useState(true) // Start loading as true

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [roleResult, userResult] = await Promise.all([
          getUserRoleAction(),
          getUserAction(params.id),
        ])

        if (roleResult) {
          setUserRole(roleResult)
        }

        if (userResult.error) {
          console.error(userResult.error)
        } else {
          setUserProfile(userResult.data)
        }
      } catch (error) {
        console.error("Failed to fetch data:", error)
      } finally {
        setLoading(false)
      }
    }

    if (params.id) {
      fetchData()
    } else {
      setLoading(false)
    }
  }, [params.id])

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading...</div>
      </div>
    )
  }

  if (!params.id) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Failed to load user profile</div>
      </div>
    )
  }

  const renderActiveForm = () => {
    if (!userProfile) {
      return (
        <div className={styles.error}>Could not load user data.</div>
      )
    }

    switch (activeForm) {
      case "profile":
        return <ProfileForm userId={params.id} />
      case "portfolio":
        return (
          <PortfolioForm
            userId={params.id}
            initialLinks={userProfile.portfolio_links}
          />
        )
      case "category":
      return (
        <CategoryForm
          userId={params.id}
          initialCategoryNames={userProfile.categories.map(cat => cat.name)}
        />
      )
      default:
        return <ProfileForm userId={params.id} />
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
          {userRole === "provider" && (
            <button
              className={`${styles.navButton} ${activeForm === "portfolio" ? styles.active : ""}`}
              onClick={() => setActiveForm("portfolio")}
            >
              Portfolio Links
            </button>
          )}
          {(userRole === "provider" || userRole === "company") &&
            <button
              className={`${styles.navButton} ${activeForm === "category" ? styles.active : ""}`}
              onClick={() => setActiveForm("category")}
            >
              Categories
            </button>
          }
          <button className={styles.deleteButton} onClick={() => setShowDeleteModal(true)}>
            Delete Account
          </button>
        </div>
        <div className={styles.formContainer}>{renderActiveForm()}</div>
        {showDeleteModal && (
          <DeleteModal userId={params.id} onClose={() => setShowDeleteModal(false)} />
        )}
      </div>
    </div>
  )
}
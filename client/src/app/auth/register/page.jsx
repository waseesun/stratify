"use client"

import { useState } from "react"
import UserTypeCard from "@/components/cards/UserTypeCard"
import {UserTypeButton} from "@/components/buttons/Buttons"
import RegisterForm from "@/components/forms/RegisterForm"
import styles from "./page.module.css"

export default function RegisterPage() {
  const [selectedUserType, setSelectedUserType] = useState(null)
  const [showForm, setShowForm] = useState(false)

  const handleUserTypeSelect = (userType) => {
    setSelectedUserType(userType)
  }

  const handleContinue = (userType) => {
    setSelectedUserType(userType)
    setShowForm(true)
  }

  const handleBack = () => {
    setShowForm(false)
    setSelectedUserType(null)
  }

  if (showForm) {
    return <RegisterForm userType={selectedUserType} onBack={handleBack} />
  }

  return (
    <div className={styles.container}>
      <div className={styles.content}>
        <h1 className={styles.title}>Choose Your Account Type</h1>
        <p className={styles.subtitle}>Select how you want to use our platform</p>

        <div className={styles.cardContainer}>
          <div className={styles.cardWrapper}>
            <UserTypeCard
              title="Company"
              description="Register as a business to hire freelancers and manage projects"
              icon="🏢"
              onClick={() => handleUserTypeSelect("company")}
              isSelected={selectedUserType === "company"}
            />
            <UserTypeButton userType="company" onClick={handleContinue} disabled={selectedUserType !== "company"}>
              Register as Company
            </UserTypeButton>
          </div>

          <div className={styles.cardWrapper}>
            <UserTypeCard
              title="Freelancer"
              description="Register as a freelancer to offer your services and find work"
              icon="👨‍💻"
              onClick={() => handleUserTypeSelect("provider")}
              isSelected={selectedUserType === "provider"}
            />
            <UserTypeButton userType="provider" onClick={handleContinue} disabled={selectedUserType !== "provider"}>
              Register as Freelancer
            </UserTypeButton>
          </div>
        </div>
      </div>
    </div>
  )
}

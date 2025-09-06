"use client"

import { useState, useEffect } from "react"
import { redirect, useRouter } from "next/navigation"
import { getUserIdAction, getUserRoleAction } from "@/actions/authActions"
import { LogoutButton } from "@/components/buttons/Buttons"
import { DEFAULT_LOGIN_REDIRECT } from "@/route"
import styles from "./Navbar.module.css"

export default function Navbar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const [userRole, setUserRole] = useState(null)
  const [userId, setUserId] = useState(null)
  const router = useRouter()

  useEffect(() => {
    const fetchUserRole = async () => {
      const result = await getUserRoleAction()
      console.log("User role:", result);
      if (result) {
        setUserRole(result)
      } else {
        setUserRole(null)
        redirect("/auth/login")
      }
    }

    const fetchUserId = async () => {
      const result = await getUserIdAction()
      console.log("User ID:", result);
      if (result) {
        setUserId(result)
      } else {
        setUserId(null)
        redirect("/auth/login")
      }
    }

    fetchUserRole()
    fetchUserId()
  }, [])

  const handleHome = () => {
    router.push(DEFAULT_LOGIN_REDIRECT)
    setIsMenuOpen(false)
  }

  const handleProfile = () => {
    router.push(`/profile/${userId}`)
    setIsMenuOpen(false)
  }

  const handleAdminDashboard = () => {
    router.push("/admin")
    setIsMenuOpen(false)
  }

  const menuItems = [
    { name: "Problems", href: "/problems" },
    { name: "Proposals", href: "/proposals" },
    { name: "Transactions", href: "/transactions" },
    { name: "Reviews", href: "/reviews" },
  ]

  return (
    <>
      <nav className={styles.navbar}>
        <div className={styles.container}>
          <div className={styles.leftSection}>
            <button className={styles.menuButton} onClick={() => setIsMenuOpen(true)}>
              <div className={styles.hamburger}>
                <span></span>
                <span></span>
                <span></span>
              </div>
            </button>
          </div>

          <div className={styles.centerSection}>
            <h1 className={styles.logo}>
              <a className={styles.stratify} href={DEFAULT_LOGIN_REDIRECT}>Stratify</a>
            </h1>
          </div>

          <div className={styles.rightSection}>
            <LogoutButton />
          </div>
        </div>
      </nav>
      
      <aside className={`${styles.sidebar} ${isMenuOpen ? styles.open : ''}`}>
        <div className={styles.sidebarHeader}>
          <h2 className={styles.sidebarTitle}>Menu</h2>
          <button className={styles.closeButton} onClick={() => setIsMenuOpen(false)}>
            &#8592;
          </button>
        </div>
        <ul className={styles.sidebarList}>
          <li className={styles.sidebarItem}>
            <a onClick={handleHome}>Home</a>
          </li>

          <li className={styles.sidebarItem}>
            <a onClick={handleProfile}>Profile</a>
          </li>
          
          {userRole === "admin" && (
            <li className={styles.sidebarItem}>
              <a onClick={handleAdminDashboard}>Admin Dashboard</a>
            </li>
          )}
          
          {menuItems.map((item) => (
            <li key={item.name} className={styles.sidebarItem}>
              <a
                href={item.href}
                onClick={() => setIsMenuOpen(false)}
              >
                {item.name}
              </a>
            </li>
          ))}
        </ul>
      </aside>
    </>
  )
}
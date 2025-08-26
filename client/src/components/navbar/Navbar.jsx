"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { getUserRoleAction } from "@/actions/authActions"
import styles from "./Navbar.module.css"

export default function Navbar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const [isProfileOpen, setIsProfileOpen] = useState(false)
  const [userRole, setUserRole] = useState(null)
  const router = useRouter()

  useEffect(() => {
    const fetchUserRole = async () => {
      const result = await getUserRoleAction()
      if (result.role) {
        setUserRole(result.role)
      }
    }
    fetchUserRole()
  }, [])

  const handleLogout = () => {
    // Implement logout logic
    router.push("/auth/login")
  }

  const handleProfile = () => {
    router.push("/profile")
  }

  const handleAdminDashboard = () => {
    router.push("/admin")
  }

  const menuItems = [
    { name: "Problems", href: "/problems" },
    { name: "Proposals", href: "/proposals" },
    { name: "Transactions", href: "/transactions" },
    { name: "Reviews", href: "/reviews" },
  ]

  return (
    <nav className={styles.navbar}>
      <div className={styles.container}>
        {/* Left Menu */}
        <div className={styles.leftSection}>
          <div className={styles.menuContainer}>
            <button className={styles.menuButton} onClick={() => setIsMenuOpen(!isMenuOpen)}>
              <div className={styles.hamburger}>
                <span></span>
                <span></span>
                <span></span>
              </div>
            </button>
            {isMenuOpen && (
              <div className={styles.dropdown}>
                {menuItems.map((item) => (
                  <a
                    key={item.name}
                    href={item.href}
                    className={styles.dropdownItem}
                    onClick={() => setIsMenuOpen(false)}
                  >
                    {item.name}
                  </a>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Center Logo */}
        <div className={styles.centerSection}>
          <h1 className={styles.logo}>Stratify</h1>
        </div>

        {/* Right Section */}
        <div className={styles.rightSection}>
          {userRole === "admin" && (
            <button className={styles.adminButton} onClick={handleAdminDashboard}>
              Admin Dashboard
            </button>
          )}
          <button className={styles.profileButton} onClick={handleProfile}>
            Profile
          </button>
          <button className={styles.logoutButton} onClick={handleLogout}>
            Logout
          </button>
        </div>
      </div>
    </nav>
  )
}


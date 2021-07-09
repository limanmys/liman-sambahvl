# LimanMYS SambaHvl Eklentisi


Basit anlamda sunucuya SambaHVL paketini kurup, sonrasında etki alanı oluşturabilen ve Samba servis durumunun görüntülenebildiği Liman Eklentisi.

Eklenti yeteneklerinin el ile yapılmasını anlatan dev.to makalesi : [Yeni bir SAMBA Etki Alanı oluşturma](https://dev.to/aciklab/yeni-bir-samba-etki-alani-olusturma-42pd)

SambaHVL temel itibari ile kullanıcının tamamen boş ya da Samba kurulumu yarım bırakılmış bir sunucu üzerinde yeni bir etki alanı oluşturmasını, göç edilmesini (migrate) ve sonrasında da bu sunucunun Samba metriklerinin görüntülenmesi amacıyla tasarlanmıştır. Buna ek olarak halihazırda Samba kurulu DC'ler de SambaHVL sayesinde pasif hale getirilebilir.


## Eklenti Görselleri

### SambaHVL kurulumu

Eğer sunucunuzda hali hazırda kurulu bir SambaHVL yoksa, eklenti bunu tespit edip Kurulum sekmesinde yönlendirir.

![smb1](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb1.PNG)

Kurulum işlemi gerçekleştirmek için butona tıklandığında ise kurulum sırasındaki loglar görüntülenir.

![smb2](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb2.PNG)

### Etki alanı kurulumu / Göç işlemi

SambaHVL kurulumu başarıyla tamamlandıktan sonra istenirse yeni bir domain ya da göç işlemi gerçekleştirebilir.

![smb3](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb3.PNG)

Bu işlemlerden biri başarı ile gerçekleştirildikten sonra eklentide erişilebilecek tüm noktalar açılmış olur.

### Samba Bilgileri

Samba bilgileri sekmesinde, paket detayları, paket güncelleme durumu, Samba servis durumu, etki alanı bilgileri gibi temel bilgiler görüntülenebilir.

Bunun haricinde kurulum esnasındaki loglar da bu sekmenin altında görüntülenebilir.

![smb4](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb4.PNG)

### Kullanıcılar

Kullanıcılar sekmesinde var olan domain kullanıcıları görüntülenebilir, yeni kullanıcılar eklenebilir.

![smb5](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb5.PNG)

### Gruplar

Gruplar sekmesinde var olan domain grupları görüntülenebilir, yeni gruplar eklenebilir.

![smb6](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb6.PNG)

### Bilgisayarlar

Bilgisayarlar sekmesinde var olan bilgisayarlar görüntülenebilir.

![smb7](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb7.PNG)

### FSMO Rollerinin Yönetilmesi

FSMO rollerinin görüntülendiği bu sekmede, eğer göç edilmiş bir sunucuya sahipseniz burada ilgili role sağ tıklayarak rol transferi gerçekleştirebilirsiniz.

![smb8](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb8.PNG)

### Replikasyon Bilgilerinin Görüntülenmesi

Göç edilmiş bir sunucuda replikasyon bilgileri görüntülenebilir, sağ tıklayarak güncellenebilir ya da son güncelleme zamanı görüntülenebilir.

![smb9](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb9.PNG)

### Site Listesi

DC üzerinde bulunan siteların görüntülendiği bu sekmede, ayrıca site oluşturma işlemi de yapılabilir.

![smb10](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb10.PNG)

### Etki Alanı Denetleyicisini Düşür

İstenilen etki alanı denetleyicisini düşürür. 

![smb11](https://github.com/zekiahmetbayar/liman-sambahvl/blob/main/images/smb11.PNG)